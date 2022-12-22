<?php

namespace App\Jobs;

use App\Entities\Label;
use Carbon\Carbon;
use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Repositories\OrderRepository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Controller nexo
 */
class ImportNexoLabelsControllerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $header = $ordersVerified = $data = [];
        $orderRepository = app(OrderRepository::class);
        $file = Storage::path('user-files/nexo-controller.csv');

        $orders = $orderRepository
            ->where([["created_at", ">", '2022-06-01']])
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
                $order = $orderRepository->find($key);

                if ($order === null || $order->created_at < '2022-06-01') {
                    continue;
                }

                $labelsToAdd[] = Label::INVOICE_OCCURS_IN_NEXO;
                if (($order->getSumOfGrossValues() - $order->refunded) !== $this->countTheValueOfInvoices($rawData)) {
                    $labelsToAdd[] = Label::GROSS_VALUE_DIFFERS_FROM_INVOICES_IN_NEXO;
                } else {
                    $labelsToAdd[] = Label::GROSS_VALUE_AGREES_FROM_INVOICES_IN_NEXO;

                }

                if ($order->hasLabel(Label::ORDER_ITEMS_REDEEMED_LABEL) && !$order->hasLabel(Label::INVOICE_OCCURS_IN_NEXO)) {
                    $labelsToAdd[] = Label::FAILURE_TO_INVOICE_DESPITE_DEPARTURE_OF_GOODS;
                }

                $orderDate = new Carbon($order->preferred_invoice_date);
                $date = new Carbon(end($rawData)['data']);

                if ($orderDate->format('Y-m') !== $date->format('Y-m') && $order->created_at > '2022-11-01') {
                    $labelsToAdd[] = Label::INVOICE_DATE_AND_PREFERRED_DATE_HAVE_DIFFERENT_MONTHS;
                }

                if ($order->hasLabel(Label::ALLEGRO_OFFERS) || !empty($order->allegro_form_id)) {
                    if (empty($order->allegro_payment_id) || empty($order->allegro_form_id) || $order->sum_of_gross_values === 0) {
                        $labelsToAdd[] = Label::OFFER_FROM_ALLEGRO_DOES_NOT_HAVE_THE_REQUIRED_PARAMS;
                    }
                }

                $ordersVerified[$order->id] = $labelsToAdd;
            }
        } catch (Throwable $ex) {
            Log::error(
                'Problem with nexo controller import' . $ex->getMessage(),
                [
                    'class' => $ex->getFile(),
                    'line' => $ex->getLine(),
                    'orderId' => $key
                ]
            );
        }

        foreach ($orders as $order) {
            dispatch(new RemoveLabelJob($order, [
                    Label::INVOICE_OCCURS_IN_NEXO,
                    Label::GROSS_VALUE_DIFFERS_FROM_INVOICES_IN_NEXO,
                    Label::FAILURE_TO_INVOICE_DESPITE_DEPARTURE_OF_GOODS,
                    Label::OFFER_FROM_ALLEGRO_DOES_NOT_HAVE_THE_REQUIRED_PARAMS,
                    Label::INVOICE_DATE_AND_PREFERRED_DATE_HAVE_DIFFERENT_MONTHS,
                    Label::GROSS_VALUE_AGREES_FROM_INVOICES_IN_NEXO
                ]
            ));
        }

        foreach ($ordersVerified as $orderId => $labelsToAdd) {
            dispatch(new AddLabelJob($orderRepository->find($orderId), $labelsToAdd));
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
