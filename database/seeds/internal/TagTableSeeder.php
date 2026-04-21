<?php

use Illuminate\Database\Seeder;
use App\Entities\Tag;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tag::create([
            'name' => '[KONSULTANT/MAGAZYNIER]',
            'handler' => 'consultantOrStorekeeper',
        ]);

        Tag::create([
            'name' => '[DANE-KUPUJACEGO]',
            'handler' => 'buyerData',
        ]);

        Tag::create([
            'name' => '[DANE-DO-DOSTAWY]',
            'handler' => 'shipmentData',
        ]);

        Tag::create([
            'name' => '[DANE-DO-FAKTURY]',
            'handler' => 'invoiceData',
        ]);

        Tag::create([
            'name' => '[WARTOSC-TOWARU-WRAZ-Z-TRANSPORTEM-DLA-NAS-W-CENACH-NETTO]',
            'handler' => 'warePriceWithShipmentForUsInNet',
        ]);

        Tag::create([
            'name' => '[WARTOSC-CALEGO-ZAMOWIENIA-W-CENACH-ZAKUPU-NETTO]',
            'handler' => 'completeOrderValueInPurchasePriceNet',
        ]);

        Tag::create([
            'name' => '[ZAMOWIENIE-W-CENACH-NETTO-ZAKUPU]',
            'handler' => 'orderPriceInNetPurchase',
        ]);

        Tag::create([
            'name' => '[ZAMOWIENIE-W-CENACH-NETTO-ZAKUPU-SPECJALNYCH]',
            'handler' => 'orderInNetSpecialPurchasePrice',
        ]);

        Tag::create([
            'name' => '[NUMER-OFERTY]',
            'handler' => 'offerNumber',
        ]);

        Tag::create([
            'name' => '[ZAMOWIENIE]',
            'handler' => 'order',
        ]);

        Tag::create([
            'name' => '[WARTOSC-ZAMOWIENIA]',
            'handler' => 'orderValue',
        ]);

        Tag::create([
            'name' => '[ADRES-DOSTAWY]',
            'handler' => 'shipmentAddress',
        ]);

        Tag::create([
            'name' => '[KOSZT-TRANSPORTU]',
            'handler' => 'shipmentCharge',
        ]);

        Tag::create([
            'name' => '[NASZ-KOSZT-TRANSPORTU]',
            'handler' => 'ourShipmentCharge',
        ]);

        Tag::create([
            'name' => '[DODATKOWY-KOSZT-POBRANIA]',
            'handler' => 'additionalObtainmentCharge',
        ]);

        Tag::create([
            'name' => '[DODATKOWY-KOSZT-OBSLUGI]',
            'handler' => 'additionalServiceCharge',
        ]);

        Tag::create([
            'name' => '[ZALICZKA-PROPONOWANA]',
            'handler' => 'instalment',
        ]);

        Tag::create([
            'name' => '[ZALICZKA-ZAKSIEGOWANA]',
            'handler' => 'instalmentBooked',
        ]);

        Tag::create([
            'name' => '[WARTOSC-ZAMOWIENIA-Z-KOSZTAMI-TRANSPORTU]',
            'handler' => 'orderValueWithShipmentCharge',
        ]);

        Tag::create([
            'name' => '[WARTOSC-ZAMOWIENIA-ZE-WSZYSTKIMI-KOSZTAMI]',
            'handler' => 'orderValueWithAllCosts',
        ]);

        Tag::create([
            'name' => '[DO-POBRANIA-PRZY-ROZLADUNKU]',
            'handler' => 'toChargeWhileUnloading',
        ]);

        Tag::create([
            'name' => '[DATA-NADANIA-PRZESYLKI]',
            'handler' => 'cargoSentDate',
        ]);

        Tag::create([
            'name' => '[UWAGI-OSOBY-ZAMAWIAJACEJ]',
            'handler' => 'commentsOfPurchaser',
        ]);

        Tag::create([
            'name' => '[UWAGI-DO-SPEDYCJI]',
            'handler' => 'commentsToShipping',
        ]);

        Tag::create([
            'name' => '[UWAGI-DO-MAGAZYNU]',
            'handler' => 'commentsToStorehouse',
        ]);

        Tag::create([
            'name' => '[TELEFON-KUPUJACEGO]',
            'handler' => 'purchaserPhoneNumber',
        ]);

        Tag::create([
            'name' => '[EMAIL-KUPUJACEGO]',
            'handler' => 'purchaserEmail',
        ]);

        Tag::create([
            'name' => '[DANE-MAGAZYNU]',
            'handler' => 'warehouseData',
        ]);
    }
}
