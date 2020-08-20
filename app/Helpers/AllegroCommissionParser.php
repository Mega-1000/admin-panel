<?php

namespace App\Helpers;

use App\Entities\OrderAllegroCommission;
use App\Entities\OrderPackage;
use App\Entities\SelTransaction;
use App\User;

class AllegroCommissionParser
{
    public const START_STRING = 'Numer zamówienia: ';
    public const SEND_STRING = 'Numer nadania: ';

    public static function CreatePack(string $letterNumber, float $cost)
    {
        return [
            'real_cost_for_company' => $cost,
            'letter_number' => $letterNumber,
            'sending_number' => '',
            'status' => OrderPackage::SENDING,
            'symbol' => 'DPD(?)',
            'quantity' => 1,
            'delivery_courier_name' => 'DPD',
            'service_courier_name' => 'DPD'
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
        $updatingOrders = [];
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            if ($firstline) {
                $firstline = false;
                continue;
            }
            try {
                $this->parseCsvForProvision($line, $updatingOrders);
                $pack = $this->parseCsvForTransport('DPD', $line);
                if (!empty($pack)) {
                    $newLetters = array_merge($newLetters, [$pack]);
                }
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        return ['new_letters' => $newLetters, 'errors' => $errors];
    }

    /**
     * @param array $line
     * @param array $updatingOrders
     * @throws \Exception
     */
    public function parseCsvForProvision(array $line, array &$updatingOrders): void
    {
        if ($line[7] == '') {
            return;
        }
        if (strpos($line[3], 'Prowizja') === false) {
            return;
        }

        $formId = $this->getNumberForParam($line[7], self::START_STRING);
        if (!$formId) {
            return;
        }
        $transaction = $this->getTransaction($formId);
        $order = $transaction->order;
        $amount = floatval(str_replace(',', '.', $line[5]));
        if (empty($order)) {
            throw new \Exception('Brak zamówienia dla zlecenie sello o id zamówienia: ' . $formId);
        }

        if (empty($updatingOrders[$order->id])) {
            $updatingOrders[$order->id] = true;
            if ($order->detailedCommissions()->count() > 0) {
                \DB::table('order_allegro_commissions')->where('order_id', $order->id)->delete();
                $order->detailedCommissions->each->delete();
            }
        }

        $commission = new OrderAllegroCommission();
        $commission->order_id = $order->id;
        $commission->amount = $amount;
        $commission->save();
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
     * @throws \Exception
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
            throw new \Exception('Brak zlecenia sello o id zamówienia: ' . $formId);
        }
        return $transaction;
    }

    public function parseCsvForTransport(string $string, array $line): array
    {
        if ($line[7] == '' || $line[3] == '') {
            return [];
        }

        if (strpos($line[3], $string) === false) {
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

        $transaction = $this->getTransaction($formId);
        $order = $transaction->order;
        $package = $order->packages->where('letter_number', $letterNumber)->first();
        $amount = floatval(str_replace(',', '.', $line[5]));
        if (empty($package)) {
            return self::setPackageDetails($letterNumber, $amount);
        }
        $package->real_cost_for_company = $amount;
        $package->save();
        return [];
    }

    private static function setPackageDetails(string $letterNumber, float $cost)
    {
        return [
            'real_cost_for_company' => $cost,
            'letter_number' => $letterNumber
        ];
    }

    /**
     * @param array $pack
     * @throws \Exception
     */
    public static function createNewPackage($pack): void
    {
        $orderParams = [
            'want_contact' => true,
            'phone' => User::CONTACT_PHONE
        ];
        $orderBuilder = new OrderBuilder();
        $orderBuilder
            ->setPackageGenerator(new BackPackPackageDivider())
            ->setPriceCalculator(new OrderPriceCalculator())
            ->setTotalTransportSumCalculator(new TransportSumCalculator)
            ->setUserSelector(new GetCustomerForNewOrder());
        ['id' => $id, 'canPay' => $canPay] = $orderBuilder->newStore($orderParams);
        $pack['order_id'] = $id;
        OrderPackage::create($pack);
    }
}
