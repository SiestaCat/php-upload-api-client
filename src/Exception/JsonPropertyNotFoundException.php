<?php

namespace Siestacat\PhpUploadApiClient\Exception;

class JsonPropertyNotFoundException extends \Exception
{
    public function __construct(\stdClass $json, string $property)
    {
        parent::__construct(sprintf('Property "%s" not found in JSON: %s', $property, json_encode($json, JSON_PRETTY_PRINT)));
    }
}