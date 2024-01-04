<?php

namespace Siestacat\PhpUploadApiClient;

use Siestacat\PhpUploadApiClient\Exception\CurlResponseBoolException;
use Siestacat\PhpUploadApiClient\Exception\CurlResponseNot200Exception;
use Siestacat\PhpUploadApiClient\Exception\FilesNotArrayException;
use Siestacat\PhpUploadApiClient\Exception\InvalidResourceArgumentTypeException;
use Siestacat\PhpUploadApiClient\Exception\JsonDecodeFailedException;
use Siestacat\PhpUploadApiClient\Exception\JsonErrorResponseException;
use Siestacat\PhpUploadApiClient\Exception\JsonPropertyNotFoundException;

class Client
{

    private ?CurlTrace $last_curl_trace = null;

    public string $base_url;

    public function __construct(string $base_url, private string $authorization_token, private bool $ssl_verify = true)
    {
        $this->base_url = self::parseBaseUrl($base_url);
    }

    public function request():string
    {
        $json = $this->parseJsonResponse($this->call_curl('request'));

        if(!property_exists($json, 'upload_token')) throw new JsonPropertyNotFoundException($json, 'upload_token');

        return $json->upload_token;
    }

    public function getUploadUrl(string $upload_token, ?string $base_url = null):string
    {
        return ($base_url === null ? $this->base_url : self::parseBaseUrl($base_url)) . 'upload/' . $upload_token;
    }

    /**
     * @return File[]
     */
    public function getFiles(string $upload_token):array
    {
        $json = $this->parseJsonResponse($this->call_curl('files/' . $upload_token));

        if(!property_exists($json, 'files')) throw new JsonPropertyNotFoundException($json, 'files');

        if(!is_array($json->files)) throw new FilesNotArrayException($json);

        $files = [];

        foreach($json->files as $file_object)
        {
            $file = new File;

            if(!property_exists($file_object, 'filename')) throw new JsonPropertyNotFoundException($file_object, 'filename');
            if(!property_exists($file_object, 'hash')) throw new JsonPropertyNotFoundException($file_object, 'hash');
            if(!property_exists($file_object, 'size')) throw new JsonPropertyNotFoundException($file_object, 'size');

            $file->filename = $file_object->filename;
            $file->hash = $file_object->hash;
            $file->size = $file_object->size;

            if(property_exists($file_object, 'mime')) $file->mime = $file_object->mime;

            $files[] = $file;
        }

        return $files;
    }

    public function getStatus(string $upload_token):bool
    {
        $json = $this->parseJsonResponse($this->call_curl('status/' . $upload_token));

        if(!property_exists($json, 'uploaded')) throw new JsonPropertyNotFoundException($json, 'uploaded');

        return $json->uploaded;
    }

    /**
     * Returns the tmp file path with the downloaded file
     */
    public function download(string $upload_token, string $hash):string
    {
        $file_path = tempnam(sys_get_temp_dir(), substr(hash('md5', random_bytes(32)), 0, 8));

        $this->call_curl('download/' . $upload_token . '/' . $hash, fopen($file_path, 'w+'));

        return $file_path;
    }

    private function parseJsonResponse(string|bool $curl_response):\stdClass
    {
        if(is_bool($curl_response)) throw new CurlResponseBoolException($this->last_curl_trace);

        $json = json_decode($curl_response);

        if(!is_object($json)) throw new JsonDecodeFailedException($curl_response, $this->last_curl_trace);

        if(property_exists($json, 'error') || (property_exists($json, 'success') && !$json->success))
        {
            throw new JsonErrorResponseException($json, $this->last_curl_trace);
        }

        return $json;
    }

    private function call_curl(string $uri, mixed $resource = null)
    {

        if(!is_null($resource) && !is_resource($resource)) throw new InvalidResourceArgumentTypeException;

        $ch = curl_init();

        $url = $this->base_url . 'api/' . $uri;

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $headers = [
            'Authorization: Basic ' . $this->authorization_token
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if(!$this->ssl_verify)
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }

        if(is_resource($resource))
        {
            curl_setopt($ch, CURLOPT_FILE, $resource);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $this->last_curl_trace = new CurlTrace($url, curl_getinfo($ch, CURLINFO_HTTP_CODE), $headers);

        if($this->last_curl_trace->response_code !== 200) throw new CurlResponseNot200Exception($this->last_curl_trace);

        curl_close($ch);

        if(is_string($response) || is_null($response)) $this->last_curl_trace->response = $response;

        return $response;
    }

    private static function parseBaseUrl(string $url):string
    {
        return substr($url, -1) === '/' ? $url : $url . '/';
    }
}