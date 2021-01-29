<?php
declare(strict_types=1);

namespace App\Services;

use App\Helpers\TokenHelper;
use App\Mail\WarehousePaymentAccept;
use Illuminate\Support\Facades\Log;

class OrderPaymentMailService
{
    public function sendWarehousePaymentAcceptMail(string $warehouseMail, int $orderId, string $amount, string $invoiceName, string $token): void
    {
        $url = route('ordersPayment.warehousePaymentConfirmation', ['token' => $token]);

        try {
            \Mailer::create()
                ->to($warehouseMail)
                ->send(new WarehousePaymentAccept($orderId, $amount, $invoiceName, $url));
        } catch (\Swift_TransportException $e) {
            Log::error('Warehouse payment accept email was not sent due to. Error: ' . $e->getMessage());
        }
    }
}
