<?php

namespace App\Services;

use App\DTO\ImportPayIn\AllegroPayInDTO;
use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Enums\AllegroImportPayInDataEnum;
use App\Factory\AllegroPayInDTOFactory;
use App\Repositories\OrderPayments;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Exception;

class AllegroImportPayInService {
    public function writeToFile(array $data, int $type = AllegroImportPayInDataEnum::API, $file, FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService): void {
        foreach ($data as $payInData) {
            $payIn = null;
            if ($type === AllegroImportPayInDataEnum::CSV) {
                $payIn = AllegroPayInDTOFactory::fromAllegroCsvData($payInData);
            } else if ($type === AllegroImportPayInDataEnum::API) {
                $payIn = AllegroPayInDTOFactory::fromAllegroApiData($payInData);
            }

            if (!in_array($payIn->operation, ['wpłata', 'zwrot', 'dopłata'])) {
                continue;
            }

            $order = Order::where('allegro_payment_id', '=', $payIn->allegroIdentifier)->first();

            try {
                if (!empty($order)) {
                    $findOrCreatePaymentForPackageService->execute(
                        OrderPackage::where('order_id', $order->id)->first(),
                    );

                    $this->settleOrder($order, $payIn);
                    return;
                }
                fputcsv($file, $payIn->toArray());
            } catch (Exception $exception) {
                Log::notice('Błąd podczas importu: ' . $exception->getMessage(), ['line' => __LINE__]);
            }
        }
    }

    /**
     * Settle orders.
     *
     * @param Order $order
     * @param AllegroPayInDTO $payIn
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function settleOrder(Order $order, AllegroPayInDTO $payIn): void
    {
        $payIn->amount = explode(" ", $payIn->amount)[0];
        Log::notice($payIn->amount);

        $declaredSum = OrderPayments::getCountOfPaymentsWithDeclaredSumFromOrder($order, $payIn->toArray()) >= 1;
        OrderPayments::updatePaymentsStatusWithDeclaredSumFromOrder($order, $payIn->toArray());

            $order->payments()->create([
                'amount' => $payIn->amount,
                'type' => 'CLIENT',
                'promise' => '',
                'external_payment_id' => $payIn->allegroIdentifier,
                'payer' => $order->customer->login,
                'operation_date' => Carbon::parse($payIn->date),
                'comments' => implode(' ', $payIn->toArray()),
                'operation_type' => 'wplata/wyplata allegro',
                'status' => $declaredSum ? 'Rozliczająca deklarowaną' : null,
            ]);
    }
}
