<?php

namespace App\Factory;

use App\DTO\Invoices\InvoiceDTO;

interface InvoiceFactoryInterface
{
    public function createInvoice(string $name, string $url): InvoiceDTO;
}
