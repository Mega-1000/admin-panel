<?php

namespace App\DTO\Invoices;

class InvoiceDTO
{
    private string $filename;
    private string $url;

    public function __construct($filename, $url)
    {
        $this->filename = $filename;
        $this->url = $url;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
