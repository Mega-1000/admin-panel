<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Controller nexo
 */
class ImportNexoLabelsControllerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?int $userId;

    public function __construct(
        public string $currentDate
    ) {
        $this->userId = Auth::user()?->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }

        $header = $ordersVerified = $data = [];
        $file = Storage::path('user-files/nexo-controller.csv');

        $orders = Order::where([["created_at", ">", $this->currentDate]])
            ->whereHas('labels', function ($query) {
                $query->where('label_id', Label::INVOICE_OCCURS_IN_NEXO);
            })->orWhereHas('labels', function ($query) {
                $query->where('label_id', Label::GROSS_VALUE_DIFFERS_FROM_INVOICES_IN_NEXO);
            })->orWhereHas('labels', function ($query) {
                $query->where('label_id', Label::FAILURE_TO_INVOICE_DESPITE_DEPARTURE_OF_GOODS);
            })
            ->get();

        try {
            if (($handle = fopen($file, 'r')) !== false) {
                while (($row = fgetcsv($handle, 3000, ';')) !== false) {
                    $row = explode(',', $row[0], 3);
                    if (!$header) {
                        $header = $row;
                    } else {
                        if (is_numeric($row[0])) {
                            $data[$row[0]][] = array_combine($header, $row);
                        }
                    }
                }

                fclose($handle);
            }

            foreach ($data as $key => $rawData) {
                $labelsToAdd = [];
                $order = Order::find($key);
                if ($order === null || $order->created_at < $this->currentDate) {
                    continue;
                }
                $orderValueFromSystem = $order->getSumOfGrossValues() - $order->refunded;
                $orderValueFromNexo = $this->countTheValueOfInvoices($rawData);

                $labelsToAdd[] = Label::INVOICE_OCCURS_IN_NEXO;
                if (!(abs($orderValueFromSystem - $orderValueFromNexo) < 0.00001)) {
                    $labelsToAdd[] = Label::GROSS_VALUE_DIFFERS_FROM_INVOICES_IN_NEXO;
                } else {
                    $labelsToAdd[] = Label::GROSS_VALUE_AGREES_FROM_INVOICES_IN_NEXO;
                }

                if ($order->hasLabel(Label::ORDER_ITEMS_REDEEMED_LABEL) && !$order->hasLabel(Label::INVOICE_OCCURS_IN_NEXO)) {
                    $labelsToAdd[] = Label::FAILURE_TO_INVOICE_DESPITE_DEPARTURE_OF_GOODS;
                }

                $orderDate = new Carbon($order->preferred_invoice_date);
                $date = new Carbon(end($rawData)['data']);

                if ($orderDate->format('Y-m') !== $date->format('Y-m')) {
                    $labelsToAdd[] = Label::INVOICE_DATE_AND_PREFERRED_DATE_HAVE_DIFFERENT_MONTHS;
                }

                $ordersVerified[$order->id] = $labelsToAdd;
            }
        } catch (Throwable $ex) {
            Log::error(
                'Problem with nexo controller import' . $ex->getMessage(),
                [
                    'class' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'orderId' => $key ?? 'unknown'
                ]
            );
        }

        foreach ($orders as $order) {
            $preventionArray = [];
            RemoveLabelService::removeLabels($order, [
                Label::INVOICE_OCCURS_IN_NEXO,
                Label::GROSS_VALUE_DIFFERS_FROM_INVOICES_IN_NEXO,
                Label::FAILURE_TO_INVOICE_DESPITE_DEPARTURE_OF_GOODS,
                Label::INVOICE_DATE_AND_PREFERRED_DATE_HAVE_DIFFERENT_MONTHS,
                Label::GROSS_VALUE_AGREES_FROM_INVOICES_IN_NEXO
            ], $preventionArray, [], Auth::user()?->id);
        }


        foreach ($ordersVerified as $orderId => $labelsToAdd) {
            $preventionArray = [];
            AddLabelService::addLabels(Order::query()->findOrFail($orderId), $labelsToAdd, $preventionArray, [], Auth::user()?->id);
        }

        Storage::disk()->delete('user-files/nexo-controller.csv');
    }

    /**
     * Count sum of invoice
     *
     * @param array $invoices
     *
     * @return float
     */
    private function countTheValueOfInvoices(array $invoices): float
    {
        $sum = 0;
        foreach ($invoices as $invoice) {
            $sum += (float)$invoice['Brutto'];
        }
        return $sum;
    }
}
