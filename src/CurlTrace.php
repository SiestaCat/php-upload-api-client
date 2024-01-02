<?php

namespace Siestacat\PhpUploadApiClient;

class CurlTrace
{
    public function __construct
    (
        public string $url,
        public ?int $response_code = null,
        public array $headers = [],
        public ?string $response = null
    )
    {}

    public function __toString()
    {
        return sprintf("URL: %s\nResponse Code: %s\nHeaders: %s", $this->url, (is_int($this->response_code) ? strval($this->response_code) : 'null'), json_encode($this->headers, JSON_PRETTY_PRINT));
    }
}