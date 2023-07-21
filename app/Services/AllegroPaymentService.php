<?php namespace App\Services;
use Carbon\Carbon;

class AllegroPaymentService extends AllegroApiService {
    protected $auth_record_id = 3;

    private $acceptedPaymentTypes = ["CONTRIBUTION", "REFUND_CHARGE", "SURCHARGE"];

    public function __construct() {
        parent::__construct();
    }

    public function getPaymentsFromPastDay(): array {
        $date = Carbon::now()->subDay();

        return $this->getPaymentsAfterDate($date);
    }

    public function getPaymentsAfterDate(Carbon $date): array {
        $dateString = urlencode($date->toIso8601String());
        $limit = 50;
        $offset = 0;
        $totalCount = 0;

        $payments = [];

        do {
            $url = $this->getRestUrl("/payments/payment-operations?occurredAt.gte={$dateString}&limit={$limit}&offset={$offset}");
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
}
