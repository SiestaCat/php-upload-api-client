<?php

namespace Siestacat\PhpUploadApiClient\Exception;

use Siestacat\PhpUploadApiClient\CurlTrace;

class CurlResponseBoolException extends \Exception
{
    public function __construct(?CurlTrace $curlTrace)
    {
        parent::__construct('Curl response is bool. Curl Info: ' . ($curlTrace ? strval($curlTrace) : 'n/a'));
    }
}