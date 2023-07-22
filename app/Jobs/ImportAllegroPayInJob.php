<?php

namespace App\Jobs;

use App\Enums\AllegroImportPayInDataEnum;
use App\Repositories\TransactionRepository;
use App\Services\AllegroImportPayInService;
use App\Services\FindOrCreatePaymentForPackageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class ImportAllegroPayInJob
 * @package App\Jobs
 *
 * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
 */
final class ImportAllegroPayInJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const CHAR_TO_REMOVE = [
        "\xEF\xBB\xBF" => '',
        '"' => '',
        'ę' => 'e',
        'ć' => 'c',
        'ą' => 'a',
        'ń' => 'n',
        'ł' => 'l',
        'ś' => 's',
        'Ł' => 'L',
        'Ż' => 'Z',
    ];

    /**
     * @var TransactionRepository
     */
    protected readonly TransactionRepository $transactionRepository;

    /**
     * @var FindOrCreatePaymentForPackageService
     */
    protected FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService;

    public function __construct(
        protected readonly UploadedFile $file
    ) {}

    public function handle(TransactionRepository $transaction, FindOrCreatePaymentForPackageService $findOrCreatePaymentForPackageService, AllegroImportPayInService $allegroImportPayInService)
    {
        $this->findOrCreatePaymentForPackageService = $findOrCreatePaymentForPackageService;
        $header = NULL;
        $fileName = 'transactionWithoutOrder.csv';
        $file = fopen($fileName, 'w');

        $this->transactionRepository = $transaction;
        $data = array();

        if (($handle = fopen($this->file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 3000, ',')) !== FALSE) {
                if (!$header) {
                    foreach ($row as &$headerName) {
                        $headerName = Str::snake(strtr($headerName, self::CHAR_TO_REMOVE));
                    }
                    $header = $row;
                    fputcsv($file, $row);
                } else {
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }

        $data = array_reverse($data);
        $allegroImportPayInService->writeToFile($data, AllegroImportPayInDataEnum::CSV, $file, $this->findOrCreatePaymentForPackageService);

        fclose($file);
        Storage::disk('local')->put('public/transaction/TransactionWithoutOrders' . date('Y-m-d') . '.csv', file_get_contents($fileName));
    }

    /**
     * Save new transaction
     *
     * @param Order $order Order object
     * @param array $data Additional data
     * @return Transaction|null
     *
     * @throws Exception
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function saveTransaction(Order $order, array $data): ?Transaction
    {
        $existingTransaction = $this->transactionRepository->select()->where('payment_id', '=', 'w-' . $data['identyfikator'])->first();
        if ($existingTransaction !== null) {
            if ($data['operacja'] == 'zwrot') {
                $paymentsToReturn = $order->payments()->where('amount', '=', $data['kwota'])->whereNull('deleted_at')->first();
                if (!empty($paymentsToReturn)) {
                    $paymentsToReturn->delete();
                }
            } else {
                return null;
            }
        }
        return $this->transactionRepository->create([
            'customer_id' => $order->customer_id,
            'posted_in_system_date' => new DateTime(),
            'posted_in_bank_date' => new DateTime($data['data']),
            'payment_id' => (($data['operacja'] === 'zwrot') ? 'z' : 'w-') . $data['identyfikator'],
            'kind_of_operation' => $data['operacja'],
            'order_id' => $order->id,
            'operator' => $data['operator'],
            'operation_value' => preg_replace('/[^.\d]/', '', $data['kwota']),
            'balance' => (float)$this->getCustomerBalance($order->customer_id) + (float)$data['kwota'],
            'accounting_notes' => '',
            'transaction_notes' => '',
            'company_name' => Transaction::NEW_COMPANY_NAME_SYMBOL
        ]);
    }

    /**
     * Calculate balance
     *
     * @param integer $customerId Customer id
     * @return float
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function getCustomerBalance(int $customerId): float
    {
        if (!empty($lastCustomerTransaction = $this->transactionRepository->findWhere([
            ['customer_id', '=', $customerId]
        ])->last())) {
            return $lastCustomerTransaction->balance;
        }

        return 0;
    }
}
