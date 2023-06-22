<?php


namespace App\Helpers;


use App\Entities\Label;
use App\Entities\Order;
use App\Entities\SelTransaction;
use App\Http\Controllers\OrdersPaymentsController;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderPaymentService;
use Exception;
use Illuminate\Support\Facades\Auth;

readonly class AllegroPaymentImporter
{
    const DATE_COLUMN_NUMBER = 0;
    const AMOUNT_COLUMN_NUMBER = 8;
    const ID_COLUMN_NUMBER = 2;
    public function __construct(
        private string $filename,
    ) {}

    /**
     * @throws Exception
     */
    public function import(): array
    {
        if (($handle = fopen($this->filename, "r")) === FALSE) {
            throw new Exception('Nie można otworzyć pliku');
        }

        $errors = [];
        $firstline = true;
        while (($line = fgetcsv($handle, 0, ",")) !== FALSE) {
            if ($firstline) {
                $firstline = false;
                continue;
            }
            try {
                $this->payForOrder($line);
            } catch (Exception $e) {
                switch ($e->getCode()) {
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                        $errors[$e->getCode()][] = $e->getMessage();
                        break;
                    default:
                        $errors['other'][] = $e->getMessage();
                        break;
                }
            }
        }
        fclose($handle);

        return $errors;
    }

    /**
     * @param array|null $line
     * @throws Exception
     */
    private function payForOrder(?array $line): void
    {
        $amount = explode(" ", $line[self::AMOUNT_COLUMN_NUMBER])[0];
        $id = $line[self::ID_COLUMN_NUMBER];

        $transaction = SelTransaction::where('tr_CheckoutFormPaymentId', $id)->orderBy('tr_Group', 'desc')->first();
        if (empty($transaction)) {
            $order = Order::where('return_payment_id', $id)->count();
            if ($order) {
                return;
            }
            throw new Exception(json_encode(['id' => $id, 'amount' => $amount]), 1);
        }
        $order = $transaction->order;

        Order::findOrfail($id);

        $payment = $order->promisePayments();
        $found = $payment->filter(function ($item) use ($amount) {
            return abs($item->amount - $amount) < 2;
        })->first();

        if (empty($found)) {
            $isPaid = $order->bookedPayments()->where('amount', $amount)->count() > 0;
            if ($isPaid) {
                throw new Exception(json_encode(['id' => $id, 'amount' => $amount]), 3);
            }
            throw new Exception(json_encode(['id' => $id, 'amount' => $amount]), 4);
        }
        $found->promise = 0;
        $found->save();
        $prev = [];
        RemoveLabelService::removeLabels($order, [Label::IS_NOT_PAID], $prev, [], Auth::user()->id);

        OrderPaymentService::dispatchLabelsForPaymentAmount($found);
    }
}
