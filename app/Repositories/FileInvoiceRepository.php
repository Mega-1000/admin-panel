<?php

namespace App\Repositories;

use App\DTO\Invoices\InvoiceDTO;
use Illuminate\Support\Facades\Storage;
use App\Entities\Order;


class FileInvoiceRepository
{
    /**
     * @param array|string $invoiceNumber
     *
     * @return int|null
     */
    public static function getInvoiceIdFromNumber(array|string $invoiceNumber): ?int
    {
        $invoiceFolder = 'public/invoices/';
        $files = Storage::files($invoiceFolder);

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            if (str_contains($filename, $invoiceNumber)) {
                return (int)explode(' ', $filename)[1];
            }
        }

        return null;
    }

    /**
     * @param Order $order
     *
     * @return InvoiceDTO[]
     */
    public function getInvoicesForOrder(Order $order): array
    {
        $orderId = $order->id;
        $invoiceFolder = 'public/order-invoices/';
        $files = Storage::files($invoiceFolder);

        $invoices = [];

        foreach ($files as $file) {
            $filename = pathinfo($file, PATHINFO_FILENAME);
            if (str_contains($filename, $orderId)) {
                $invoiceUrl = Storage::url($file);
                $invoices[] = new InvoiceDTO($filename, $invoiceUrl);
            }
        }

        return $invoices;
    }
}
