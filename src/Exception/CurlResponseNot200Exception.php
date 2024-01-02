<?php

namespace Siestacat\PhpUploadApiClient\Exception;

use Siestacat\PhpUploadApiClient\CurlTrace;

class CurlResponseNot200Exception extends \Exception
{
    public function __construct(CurlTrace $curlTrace)
    {
        parent::__construct('HTTP response code is not 200. Info: ' . strval($curlTrace));
    }
}