<?php

namespace App\Services;

use App\DTO\AllegroPayment\AllegroReturnDTO;
use Carbon\Carbon;

class AllegroPaymentService extends AllegroApiService {
    protected $auth_record_id = 2;

    private $acceptedPaymentTypes = ["CONTRIBUTION", "REFUND_CHARGE", "SURCHARGE"];

    public function getPaymentsFromLastDay(): array 
    {
        $startDate = Carbon::yesterday()->startOfDay();
        $endDate = Carbon::yesterday()->endOfDay();

        return $this->getPaymentsBetweenDates($startDate, $endDate);
    }

    public function getPaymentsBetweenDates(Carbon $startDate, Carbon $endDate): array 
    {
        $startDateString = $startDate->format('Y-m-d\TH:i:s\Z');
        $endDateString = $endDate->format('Y-m-d\TH:i:s\Z');
        $limit = 50;
        $offset = 0;

        $payments = [];

        do {
            $query_params = [
                'occurredAt.gte' => $startDateString,
                'occurredAt.lte' => $endDateString,
                'limit' => $limit,
                'offset' => $offset,
            ];

            $url = $this->getRestUrl("/payments/payment-operations?" . http_build_query($query_params));
            if (!($response = $this->request('GET', $url, []))) {
                break;
            }

            $paymentOperations = array_filter($response['paymentOperations'], function ($paymentOperation) {
                return in_array($paymentOperation['type'], $this->acceptedPaymentTypes);
            });

            $totalCount = $response['totalCount'];
            $offset += $limit;

            $payments = array_merge($payments, $paymentOperations);
        } while ($offset < $totalCount);

        foreach ($payments as &$payment) {
            if ($payment["type"] !== "CONTRIBUTION") {
                continue;
            }

            $url = $this->getRestUrl("/order/checkout-forms?payment.id=" . $payment['payment']['id']);
            if (!($response = $this->request('GET', $url, []))) {
                continue;
            }

            $checkoutForm = $response['checkoutForms'][0];

            $itemsString = "";
            $deliveryCost = "0.00 PLN";

            foreach ($checkoutForm['lineItems'] as $lineItem) {
                $itemsString .= $lineItem['offer']['id'] . ";";
                $itemsString .= $lineItem["offer"]["name"] . ";";
                $itemsString .= $lineItem["quantity"] . "|";
            }

            if (str_ends_with($itemsString, "|")) {
                $itemsString = substr($itemsString, 0, -1);
            }


            if (array_key_exists('delivery', $checkoutForm)) {
                $deliveryCost = $checkoutForm['delivery']['cost']['amount'] . " " . $checkoutForm['delivery']['cost']['currency'];
            }

            $payment['offer'] = $itemsString;
            $payment['deliveryCost'] = $deliveryCost;
        }


        return $payments;
    }

    public function getRefundsByPaymentId(string $paymentId): array 
    {
        $url = $this->getRestUrl("/payments/refunds?payment.id=" . $paymentId);

        if (!($response = $this->request('GET', $url, []))) {
            return [];
        }

        $uncancelledRefunds = array_filter($response['refunds'], function ($refund) {
            return $refund['status'] !== "CANCELED";
        });

        return $uncancelledRefunds;
    }

    /**
     * tworzy zwrot płatności
     * @param AllegroReturnDTO $allegroReturnDTO
     * @return bool - czy udało się stworzyć zwrot płatności
     */
    public function initiatePaymentRefund(AllegroReturnDTO $allegroReturnDTO): array|bool
    {
        $url = $this->getRestUrl("/payments/refunds");

        $data = $allegroReturnDTO->toAllegroRefundArray();

        if (!($response = $this->request('POST', $url, $data))) {
            return false;
        }

        return $response;
    }

    /**
     * Tworzy zwrot prowizji dla podanego lineItemId
     * @param string $lineItemId
     * @param int $quantity
     * @return bool - czy udało się stworzyć zwrot prowizji
     */
    public function createCommissionRefund(string $lineItemId, int $quantity): bool 
    {
        $data = [
            "lineItem" => [
                "id" => $lineItemId
            ],
            "quantity" => $quantity
        ];

        $url = $this->getRestUrl("/order/refund-claims");

        if (!($response = $this->request('POST', $url, $data))) {
            return false;
        }

        return true;
    }
}
