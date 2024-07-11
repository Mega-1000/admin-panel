<?php

namespace App\Services;

use App\DTO\ControllSubjectInvoice\ControllSubjectInvoiceDTO;
use App\Entities\BuyingInvoice;
use App\Entities\Order;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ControllSubjectInvoiceBuyingService
{
    private Collection $orders;

    /**
     * @param array<ControllSubjectInvoiceDTO> $data
     * @return void
     */
    public function handle(array $data): void
    {
        $groupedData = $this->groupDataByOrder($data);
        $this->processBuyingInvoices($groupedData);

        $orders = Order::with(['labels', 'items'])
            ->whereHas('labels', function ($query) {
                $query->where('labels.id', 263);
            })
            ->get();

        $buyingInvoices = $this->getBuyingInvoiceTotals($orders->pluck('id'));

        $labelsToAdd = [];
        $labelsToRemove = [];

        foreach ($orders as $order) {
            $totalItemsCost = $this->calculateTotalItemsCost($order);
            $totalGross = $buyingInvoices[$order->id] ?? 0;

            if ($order->labels->contains('id', 65) && $totalGross == round($totalItemsCost, 2)) {
                $labelsToAdd[$order->id][] = 264;
                $labelsToRemove[$order->id][] = 263;
            } else {
                $labelsToAdd[$order->id][] = 263;
                $labelsToRemove[$order->id][] = 264;
            }
        }

        $this->bulkUpdateLabels($labelsToAdd, $labelsToRemove);
    }

    private function groupDataByOrder(array $data): array
    {
        return collect($data)->groupBy(function ($dto) {
            return preg_replace('/\D/', '', $dto->notes);
        })->toArray();
    }

    private function processBuyingInvoices(array $groupedData): void
    {
        $buyingInvoices = [];
        $orderIds = [];

        foreach ($groupedData as $orderNotes => $dtos) {
            $orderId = (int)$orderNotes;
            $orderIds[] = $orderId;

            foreach ($dtos as $dto) {
                if (!$this->buyingInvoiceExists($dto->number)) {
                    $buyingInvoices[] = [
                        'order_id' => $orderId,
                        'value' => $this->parseGrossValue($dto->gross),
                        'invoice_number' => $dto->number,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        if (!empty($buyingInvoices)) {
            $this->insertBuyingInvoices($buyingInvoices);
        }

        $this->orders = Order::whereIn('id', $orderIds)->get();
    }

    private function buyingInvoiceExists(string $invoiceNumber): bool
    {
        return BuyingInvoice::where('invoice_number', $invoiceNumber)->exists();
    }

    private function parseGrossValue(string $gross): float
    {
        return (float)str_replace([',', ' '], ['.', ''], $gross);
    }

    private function insertBuyingInvoices(array $buyingInvoices): void
    {
        DB::table('buying_invoices')->insert($buyingInvoices);
    }

    private function getBuyingInvoiceTotals(Collection $orderIds): Collection
    {
        return BuyingInvoice::whereIn('order_id', $orderIds)
            ->select('order_id', DB::raw('SUM(value) as total_gross'))
            ->groupBy('order_id')
            ->get()
            ->keyBy('order_id')
            ->map(function ($item) {
                return $item->total_gross;
            });
    }

    private function calculateTotalItemsCost(Order $order): float
    {
        $sumOfPurchase = $order->items->sum(function ($item) {
            return ($item['net_purchase_price_commercial_unit_after_discounts'] ?? 0) * ($item['quantity'] ?? 0);
        });

        return ($sumOfPurchase * 1.23) + $order->shipment_price_for_us;
    }

    private function bulkUpdateLabels(array $labelsToAdd, array $labelsToRemove): void
    {
        DB::transaction(function () use ($labelsToAdd, $labelsToRemove) {
            foreach ($labelsToAdd as $orderId => $labels) {
                AddLabelService::addLabels(Order::find($orderId), $labels, [], []);
            }

            foreach ($labelsToRemove as $orderId => $labels) {
                RemoveLabelService::removeLabels(Order::find($orderId), $labels, [], [], auth()->id());
            }
        });
    }
}
