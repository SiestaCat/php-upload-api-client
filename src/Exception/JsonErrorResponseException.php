<?php

namespace Siestacat\PhpUploadApiClient\Exception;

use Siestacat\PhpUploadApiClient\CurlTrace;

class JsonErrorResponseException extends \Exception
{
    public function __construct(\stdClass $json, ?CurlTrace $curlTrace)
    {
        parent::__construct(sprintf('Error response JSON: %s', json_encode($json, JSON_PRETTY_PRINT)) . "\nCurl Info:" . ($curlTrace ? strval($curlTrace) : 'n/a'));
    }
}