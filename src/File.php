<?php

namespace Siestacat\PhpUploadApiClient;

class File
{
    public string $filename;

    public string $hash;

    public int $size;

    public ?string $mime = null;
}