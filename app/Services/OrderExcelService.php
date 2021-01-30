<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\AllegroExcel\AllegroHeaders;
use App\Enums\AllegroExcel\OrderHeaders;
use App\Enums\AllegroExcel\PaymentsHeader;
use App\Enums\AllegroExcel\SheetNames;
use App\Exports\OrdersAllegroExport;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class OrderExcelService
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function generateAllegroPaymentsExcel(Request $request): BinaryFileResponse
    {
        $orderData = [];
        $allegroPayments = [];
        $clientPayments = [];

        $orderData[] = $this->prepareHeadersForSheet(SheetNames::ORDER_DATA);
        $allegroPayments[] = $this->prepareHeadersForSheet(SheetNames::ALLEGRO_PAYMENTS);
        $clientPayments[] = $this->prepareHeadersForSheet(SheetNames::CLIENT_PAYMENTS);

        $orders = $this->orderRepository->getOrdersForExcelFile($request->input('allegro_from'), $request->input('allegro_to'));

        $orders->each(function($order) use (&$orderData, &$allegroPayments, &$clientPayments) {
            if($order->getSentPackages()->count() === 0) {
                $orderData[] = [
                    $order->id,
                    '',
                    '',
                    $order->selloTransaction->tr_CheckoutFormId ?? '',
                    $order->getItemsGrossValue(),
                    $order->additional_service_cost,
                    $order->additional_cash_on_delivery_cost,
                    $order->getOrderProfit(),
                    '',
                    '',
                    0,
                ];
            } else {
                $order->getSentPackages()->each(function($package) use ($order, &$orderData) {
                    $orderData[] = [
                        $order->id,
                        $package->letter_number,
                        $package->cash_on_delivery ?? 0,
                        $order->selloTransaction->tr_CheckoutFormId ?? '',
                        $order->getItemsGrossValue(),
                        $order->additional_service_cost,
                        $order->additional_cash_on_delivery_cost,
                        $order->getOrderProfit(),
                        $package->cost_for_company,
                        $package->cost_for_client,
                        0,
                    ];
                });
            }

            $clientPayments[] = [$order->id, $order->bookedPaymentsSum()];
            if($order->selloTransaction === null) {
                return true;
            }
            $allegroPayments[] = [
                $order->id,
                $order->selloTransaction->tr_CheckoutFormPaymentId,
                $order->promisePaymentsSum(),
                $order->refund_id,
                $order->refunded,
            ];
        });

        return Excel::download(new OrdersAllegroExport($orderData, $allegroPayments, $clientPayments), 'allegrox.xlsx');
    }

    private function prepareHeadersForSheet(string $sheetName): array
    {
        switch($sheetName) {
            case SheetNames::ORDER_DATA:
                return [
                    OrderHeaders::getDescription(OrderHeaders::ORDER_ID),
                    OrderHeaders::getDescription(OrderHeaders::PACKAGE_LETTER_NUMBER),
                    OrderHeaders::getDescription(OrderHeaders::CASH_ON_DELIVERY_AMOUNT),
                    OrderHeaders::getDescription(OrderHeaders::ALLEGRO_ORDER_ID),
                    OrderHeaders::getDescription(OrderHeaders::ORDER_ITEMS_SUM),
                    OrderHeaders::getDescription(OrderHeaders::ADDITIONAL_SERVICE_COST),
                    OrderHeaders::getDescription(OrderHeaders::ADDITIONAL_CASH_ON_DELIVERY_COST),
                    OrderHeaders::getDescription(OrderHeaders::ORDER_PROFIT),
                    OrderHeaders::getDescription(OrderHeaders::CLIENT_PACKAGE_COST),
                    OrderHeaders::getDescription(OrderHeaders::FIRM_PACKAGE_COST),
                    OrderHeaders::getDescription(OrderHeaders::REAL_PACKAGE_COST)
                ];
            case SheetNames::ALLEGRO_PAYMENTS:
                return [
                    AllegroHeaders::getDescription(AllegroHeaders::ORDER_ID),
                    AllegroHeaders::getDescription(AllegroHeaders::ALLEGRO_PAYMENT_ID),
                    AllegroHeaders::getDescription(AllegroHeaders::PROMISE_PAYMENTS_SUM),
                    AllegroHeaders::getDescription(AllegroHeaders::REFUND_ID),
                    AllegroHeaders::getDescription(AllegroHeaders::REFUNDED),
                ];
            case SheetNames::CLIENT_PAYMENTS:
                return [
                    PaymentsHeader::getDescription(PaymentsHeader::ORDER_ID),
                    PaymentsHeader::getDescription(PaymentsHeader::PAYMENT_SUM)
                ];
            default:
                return [];
        }
    }
}
