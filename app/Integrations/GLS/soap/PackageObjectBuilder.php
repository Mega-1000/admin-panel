<?php


namespace App\Integrations\GLS\soap;


use App\Entities\OrderPackage;
use stdClass;

class PackageObjectBuilder
{
    public static function preparePackageObject(OrderPackage $package, $session): stdClass
    {
        $oPackage = new stdClass();
        $oPackage->session = $session;
        $oPackage->consign_prep_data = new stdClass();
        $address = $package->order->getDeliveryAddress();
        $oPackage->consign_prep_data->rname1 = $address->firstname ?: $address->firmname;
        $oPackage->consign_prep_data->rname2 = $address->lastname;
        $oPackage->consign_prep_data->rname3 = empty($address->firstname) ? '' : $address->firmname;

        $oPackage->consign_prep_data->rcountry = 'PL';
        $oPackage->consign_prep_data->rzipcode = $address->postal_code;
        $oPackage->consign_prep_data->rcity = $address->city;
        $oPackage->consign_prep_data->rstreet = $address->address . ' ' . $address->flat_number;

        $oPackage->consign_prep_data->rphone = $address->phone;
        $oPackage->consign_prep_data->rcontact = $address->email;

        $oPackage->consign_prep_data->date = $package->shipment_date;

        if ($package->cash_on_delivery > 0) {
            $oPackage->consign_prep_data->srv_bool = new stdClass();
            $oPackage->consign_prep_data->srv_bool->cod = 1;
            $oPackage->consign_prep_data->srv_bool->cod_amount = $package->cash_on_delivery;
        }

        $oPackage->consign_prep_data->parcels = new stdClass();
        $oParcel = new stdClass();
        $oParcel->reference = "$package->order_id / $package->number";
        $oParcel->weight = $package->weight;
        $oPackage->consign_prep_data->parcels->items[] = $oParcel;
        return $oPackage;
    }

}
