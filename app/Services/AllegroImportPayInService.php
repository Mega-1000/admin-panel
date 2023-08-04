<?php

namespace App\Services;

use App\DTO\ImportPayIn\AllegroPayInDTO;
use App\Entities\Order;
use App\Entities\OrderPackage;
use App\Factory\AllegroPayInDTOFactory;
use App\Mail\AllegroPayInMail;
use App\Repositories\OrderPayments;
use Carbon\Carbon;
use App\Facades\Mailer;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Storage;

readonly class AllegroImportPayInService 
{
    public function __construct(
        private AllegroPaymentService $allegroPaymentService,
        private FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService,
    ) {}

    public function importLastDayPayInsFromAllegroApi(): void 
    {
        $files = Storage::disk('allegroPayInDisk')->files();
        foreach ($files as $file) {
            Storage::disk('allegroPayInDisk')->delete($file);
        }

        $payments = $this->allegroPaymentService->getPaymentsFromLastDay();

        $filename = "transactionWithoutOrder.csv";
        $file = fopen($filename, 'w');

        fputcsv($file, AllegroPayInDTO::$headers);

        $payments = array_map(function ($payment) {
            return AllegroPayInDTOFactory::fromAllegroApiData($payment);
        }, $payments);

        $this->import($payments, $file);

        fclose($file);

        $yesterdayDate = Carbon::yesterday()->format('Y-m-d');

        $newFilePath = 'public/transaction/TransactionWithoutOrdersFromAllegro' . $yesterdayDate . '.csv';

        Storage::disk('allegroPayInDisk')->put($newFilePath, file_get_contents($filename));

        Mailer::create()
            ->to(config('allegro.payInMailReceiver'))->send(new AllegroPayInMail($newFilePath));
    }

    /**
     * @param AllegroPayInDTO[] $data
     * @param resource $file
     * @return void
     */
    public function import(array $data, $file): void 
    {
        foreach ($data as $payIn) {
            if (!in_array($payIn->operation, ['wpłata', 'zwrot', 'dopłata'])) {
                continue;
            }

            $order = Order::where('allegro_payment_id', '=', $payIn->allegroIdentifier)->first();

            var_dump($order);

            try {
                if (!empty($order)) {
                    $this->findOrCreatePaymentForPackageService->execute(
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

        $existingPayment = $order->payments()
            ->where('amount', $payIn->amount)
            ->where('operation_type', 'wplata/wyplata allegro')
            ->first();

        if (empty($existingPayment)) {
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
}
