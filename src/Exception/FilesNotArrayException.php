<?php

namespace Siestacat\PhpUploadApiClient\Exception;

class FilesNotArrayException extends \Exception
{
    public function __construct(\stdClass $json)
    {
        parent::__construct(sprintf('Files property is not array in JSON: %s', json_encode($json, JSON_PRETTY_PRINT)));
    }
}