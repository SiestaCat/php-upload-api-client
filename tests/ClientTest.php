<?php

namespace Siestacat\PhpUploadApiClient\Tests;
use PHPUnit\Framework\TestCase;
use Siestacat\PhpUploadApiClient\Client;
use Siestacat\RandomFileGenerator\RandomFileGenerator;
use function Symfony\Component\String\u;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;

class ClientTest extends TestCase
{

    private ?MockWebServer $server = null;

    private ?string $base_url = null;

    public function setUp():void
    {
        $this->server = new MockWebServer;
        $this->server->start();

        $this->server->setResponseOfPath('/api/request', new Response(json_encode((object) ['upload_token' => 'my_token'])));
        $this->server->setResponseOfPath('/api/files/my_token', new Response(json_encode((object) ['success' => true, 'files' => $this->getSamplesFilesArray()])));
        foreach($this->getSamplesFilesArray() as $sample_file)
        {
            $this->server->setResponseOfPath('/api/download/my_token/' . $sample_file->hash, new Response($sample_file->filename));
        }
        
        $this->server->setResponseOfPath('/upload/my_token', new Response(json_encode((object) ['success' => true])));

        $this->base_url = $this->server->getServerRoot();
    }

    public function test_request()
    {
        $this->assertIsString($this->getClient()->request());
    }

    public function test_getFilesAndDownload()
    {
        $client = $this->getClient();

        $upload_token = $client->request();

        $files = $client->getFiles($upload_token);

        $this->assertTrue(count($files) > 0, 'Api returned files count is > 0');

        foreach($files as $file)
        {
            $downloaded_file = $client->download($upload_token, $file->hash);

            $this->assertTrue(is_file($downloaded_file) && is_readable($downloaded_file));

            $this->assertNotEmpty(file_get_contents($downloaded_file));
        }
    }

    private function getClient():Client
    {
        return new Client($this->base_url, 'changeme');
    }

    private function getSamplesFilesArray():array
    {
        return
        [
            (object)
            [
                'filename' => 'beach.jpg',
                'mime' => 'image/jpg',
                'size' => 123456,
                'hash' => 'hash_1'
            ],
            (object)
            [
                'filename' => 'newyork.jpg',
                'size' => 5454,
                'hash' => 'hash_2'
            ]
        ];
    }
}