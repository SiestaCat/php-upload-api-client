<?php

namespace Siestacat\PhpUploadApiClient\Exception;

class InvalidResourceArgumentTypeException extends \Exception
{
    public function __construct()
    {
        parent::__construct('$resource argument should by resource type if is not null');
    }
}