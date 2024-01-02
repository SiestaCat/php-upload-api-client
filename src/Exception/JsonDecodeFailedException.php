<?php

namespace Siestacat\PhpUploadApiClient\Exception;

use Siestacat\PhpUploadApiClient\CurlTrace;

class JsonDecodeFailedException extends \Exception
{
    public function __construct(string $json_str, ?CurlTrace $curlTrace)
    {
        parent::__construct
        (
            sprintf('Unable to decode JSON. Error: %s Error code: %s. JSON STR:', json_last_error_msg(), json_last_error())
            . "\n" . $json_str
            . "\nCurl Info: " . ($curlTrace?strval($curlTrace):'n/a')
        );
    }
}