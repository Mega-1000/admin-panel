<?php


namespace App\Helpers;


use App\Entities\Label;
use App\Entities\SelTransaction;
use App\Http\Controllers\OrdersPaymentsController;
use App\Jobs\RemoveLabelJob;

class AllegroPaymentImporter
{
    const DATE_COLUMN_NUMBER = 0;
    const AMOUNT_COLUMN_NUMBER = 8;
    const ID_COLUMN_NUMBER = 2;
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function import()
    {
        if (($handle = fopen($this->filename, "r")) === FALSE) {
            throw new \Exception('Nie można otworzyć pliku');
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
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        fclose($handle);
        return $errors;
    }

    /**
     * @param array|null $line
     * @throws \Exception
     */
    private function payForOrder(?array $line): void
    {
        $date = $line[self::DATE_COLUMN_NUMBER];
        $amount = explode(" ", $line[self::AMOUNT_COLUMN_NUMBER])[0];
        $id = $line[self::ID_COLUMN_NUMBER];

        $transaction = SelTransaction::where('tr_CheckoutFormId', $id)->where('tr_Group', 1)->first();
        if (empty($transaction)) {
            throw new \Exception('Nie znaleziono transakcji o id: ' . $id);
        }
        $order = $transaction->order;
        if (empty($order)) {
            throw new \Exception('Nie znaleziono zamówienia dla transakcji o id: ' . $id);
        }
        $payment = $order->promisePayments();
        $found = $payment->filter(function ($item) use ($amount) {
            return $item->amount == $amount;
        })->first();

        if (empty($found)) {
            throw new \Exception('Nie znaleziono transakcji o id: ' . $id . ' na kwotę: ' . $amount);
        }
        $found->promise = 0;
        $found->save();
        dispatch_now(new RemoveLabelJob($order->id, [Label::IS_NOT_PAID]));
        OrdersPaymentsController::dispatchLabelsForPaymentAmount($found);
    }
}
