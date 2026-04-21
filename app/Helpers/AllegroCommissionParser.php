<?php

namespace App\Helpers;

use App\Entities\Order;
use App\Entities\OrderAllegroCommission;
use App\Entities\OrderPackage;
use App\Entities\Product;
use App\Entities\SelTransaction;
use App\Enums\PackageStatus;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class AllegroCommissionParser
{
    public const START_STRING = 'Numer zamówienia: ';
    public const SEND_STRING = 'Numer nadania: ';
    private $productId;

    public function __construct()
    {
        $this->productId = Product::getDefaultProduct()->id;
    }

    public static function CreatePack(string $letterNumber, float $cost, string $courierName): array
    {
        return [
            'real_cost_for_company' => $cost,
            'letter_number' => $letterNumber,
            'sending_number' => '',
            'status' => PackageStatus::SENDING,
            'symbol' => $courierName . '(?)',
            'quantity' => 1,
            'delivery_courier_name' => $courierName,
            'service_courier_name' => $courierName
        ];
    }

    /**
     * @param $handle
     * @return array[]
     */
    public function parseFile($handle): array
    {
        $firstline = true;
        $errors = [];
        $newLetters = [];
        $newOrders = [];
        $updatingOrders = [];
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            if ($firstline) {
                $firstline = false;
                continue;
            }
            try {
                $new = $this->parseCsvForProvision($line, $updatingOrders);
                if (!empty($new)) {
                    $newOrders [] = $new;
                }
                $pack = $this->parseCsvForTransport('DPD', $line);
                if (!empty($pack)) {
                    $newLetters = array_merge($newLetters, [$pack]);
                }
                $pack = $this->parseCsvForTransport('InPost', $line);
                if (!empty($pack)) {
                    $newLetters = array_merge($newLetters, [$pack]);
                }
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return ['new_letters' => $newLetters, 'new_orders' => $newOrders, 'errors' => $errors];
    }

    /**
     * @param array $line
     * @param array $updatingOrders
     * @throws Exception
     */
    public function parseCsvForProvision(array $line, array &$updatingOrders): string
    {
        if ($line[7] == '') {
            return '';
        }
        if (strpos($line[3], 'Prowizja') === false) {
            return '';
        }

        $formId = $this->getNumberForParam($line[7], self::START_STRING);
        if (!$formId) {
            return '';
        }
        $transaction = $this->getTransaction($formId);
        if (empty($transaction)) {
            $order = Order::where('allegro_form_id', $formId)->first();
        } else {
            $order = $transaction->order;
        }
        if (empty($order)) {
            return $formId;
        }
        $amount = abs(floatval(str_replace(',', '.', $line[5])));

        if (empty($updatingOrders[$order->id])) {
            $updatingOrders[$order->id] = true;
            if ($order->detailedCommissions()->count() > 0) {
                \DB::table('order_allegro_commissions')->where('order_id', $order->id)->delete();
            }
        }

        $commission = new OrderAllegroCommission();
        $commission->order_id = $order->id;
        $commission->amount = $amount;
        $commission->save();
        return '';
    }

    /**
     * @param $line
     * @param $needle
     * @return false|string
     */
    public function getNumberForParam($line, $needle)
    {
        $start = strpos($line, $needle);
        if ($start === false) {
            return false;
        }
        $start += strlen($needle);

        $end = strpos($line, ',', $start);

        if ($end === false) {
            $end = strlen($line);
        }

        $formId = substr($line, $start, $end - $start);
        return $formId;
    }

    /**
     * @param string $formId
     * @return mixed
     * @throws Exception
     */
    public function getTransaction(string $formId)
    {
        $isGroup = SelTransaction::where('tr_CheckoutFormId', $formId)->count() > 1;
        if ($isGroup) {
            $transaction = SelTransaction::where('tr_CheckoutFormId', $formId)->where('tr_Group', 1)->first();
        } else {
            $transaction = SelTransaction::where('tr_CheckoutFormId', $formId)->first();
        }
        if (empty($transaction)) {
            return false;
        }
        return $transaction;
    }

    public function parseCsvForTransport(string $courierName, array $line): array
    {
        if ($line[7] == '' || $line[3] == '') {
            return [];
        }

        if (strpos($line[3], $courierName) === false) {
            return [];
        }
        $formId = $this->getNumberForParam($line[7], self::START_STRING);
        if (!$formId) {
            return [];
        }

        $letterNumber = $this->getNumberForParam($line[7], self::SEND_STRING);
        if (!$letterNumber) {
            return [];
        }
        switch ($line[3]) {
            case 'DPD - Kurier opłaty dodatkowe':
            case 'Przesyłka DPD':
                $deliveryCourier = 'DPD';
                break;
            case 'InPost - opłaty dodatkowe':
                $deliveryCourier = 'INPOST';
                break;
        }

        $allegroDate = Carbon::parse($line[0]);

        $package = OrderPackage::where('letter_number', $letterNumber)->first();
        $amount = floatval(str_replace(',', '.', $line[5]));
        if (empty($package)) {
            return self::setPackageDetails($letterNumber, $amount, $courierName, $formId, $deliveryCourier);
        }
        DB::table('allegro_package')->insert([
            'package_id' => $package->id,
            'allegro_operation_date' => $allegroDate,
            'package_spedition_company_name' => $deliveryCourier,
            'package_delivery_company_name' => $deliveryCourier,
        ]);
        $package->real_cost_for_company = $amount;
        $package->save();
        return [];
    }

    private static function setPackageDetails(string $letterNumber, float $cost, string $courierName, $formId, string $deliveryCourier)
    {
        return [
            'real_cost_for_company' => $cost,
            'letter_number' => $letterNumber,
            'courier_name' => $courierName,
            'form_id' => $formId,
            'delivery_courier_name' => $deliveryCourier
        ];
    }

    /**
     * @param array $pack
     * @throws Exception
     */
    public function createNewPackage($pack, $formId): void
    {
        $id = $this->createNewOrder($formId);
        $pack['order_id'] = $id;
        OrderPackage::create($pack);
    }

    /**
     * @param $formId
     * @return mixed
     * @throws Exception
     */
    public function createNewOrder($formId)
    {
        $orderParams = [
            'want_contact' => true,
            'phone' => User::CONTACT_PHONE
        ];
        $prices = [
            'gross_selling_price_commercial_unit' => 0,
            'net_selling_price_commercial_unit' => 0
        ];
        $override = [];
        $override[$this->productId] = $prices;
        $priceOverrider = new OrderPriceOverrider($override);

        $orderBuilder = new OrderBuilder();
        $orderBuilder
            ->setPriceOverrider($priceOverrider)
            ->setPackageGenerator(new BackPackPackageDivider())
            ->setPriceCalculator(new OrderPriceCalculator())
            ->setTotalTransportSumCalculator(new TransportSumCalculator)
            ->setUserSelector(new GetCustomerForNewOrder());
        ['id' => $id, 'canPay' => $canPay] = $orderBuilder->newStore($orderParams);
        $order = Order::find($id);
        $order->allegro_form_id = $formId;
        $order->save();
        return $id;
    }
}
