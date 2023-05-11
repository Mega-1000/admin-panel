<?php

namespace App\Repositories;

use App\Entities\Order;

interface InvoiceRepositoryInterface
{
    public function getInvoicesForOrder(Order $order): array;
}
