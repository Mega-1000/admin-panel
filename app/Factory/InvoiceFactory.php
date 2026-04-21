<?php

namespace App\Factory;

use App\DTO\Invoices\InvoiceDTO;

class InvoiceFactory
{
    public function createInvoice(string $name, string $url): InvoiceDTO
    {
        return new InvoiceDTO($name, $url);
    }
}
