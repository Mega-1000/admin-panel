<?php

namespace App\Traits;

use App\Integrations\Pocztex\adresType;

trait Pocztex
{
    /**
     * Zwraca xml dla adresu
     *
     * @return adresType
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function getAddress(): adresType
    {
        $address = new adresType();
        $address->nazwa = $this->data['delivery_address']['firstname'];
        $address->nazwa2 = $this->data['delivery_address']['lastname'];
        $address->ulica = $this->data['delivery_address']['address'];
        $address->numerDomu = $this->data['delivery_address']['flat_number'];
        $address->miejscowosc = $this->data['delivery_address']['city'];
        $address->kodPocztowy = $this->data['delivery_address']['postal_code'];
        $address->telefon = $this->data['delivery_address']['phone'];
        $address->email = $this->data['delivery_address']['email'];
        $address->osobaKontaktowa = $this->data['delivery_address']['firstname'] . ' ' . $this->data['delivery_address']['lastname'];

        return $address;
    }

    /**
     * Zwraca adres do dostawy
     *
     * @return adresType
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    private function getPickupAddress(): adresType
    {
        $pickupAddress = new adresType();

        $pickupAddress->nazwa = $this->data['pickup_address']['firstname'];
        $pickupAddress->nazwa2 = $this->data['pickup_address']['lastname'];
        $pickupAddress->ulica = $this->data['pickup_address']['address'];
        $pickupAddress->numerDomu = $this->data['pickup_address']['flat_number'];
        $pickupAddress->miejscowosc = $this->data['pickup_address']['city'];
        $pickupAddress->kodPocztowy = $this->data['pickup_address']['postal_code'];
        $pickupAddress->telefon = $this->data['pickup_address']['phone'];
        $pickupAddress->email = $this->data['pickup_address']['email'];
        $pickupAddress->kraj = 'Polska';
        $pickupAddress->osobaKontaktowa = $this->data['pickup_address']['firstname'] . ' ' . $this->data['pickup_address']['lastname'];

        return $pickupAddress;
    }

    private function getAddressElement($dom)
    {
        $addressElement = $dom->createElement('miejsceDoreczenia');
        $nazwa = $dom->createAttribute('nazwa');
        $nazwa->value = $this->data['delivery_address']['firstname'];
        $nazwa2 = $dom->createAttribute('nazwa2');
        $nazwa2->value = $this->data['delivery_address']['lastname'];
        $ulica = $dom->createAttribute('ulica');
        $ulica->value = $this->data['delivery_address']['address'];
        $numerDomu = $dom->createAttribute('numerDomu');
        $numerDomu->value = $this->data['delivery_address']['flat_number'];
        $miejscowosc = $dom->createAttribute('miejscowosc');
        $miejscowosc->value = $this->data['delivery_address']['city'];
        $kodPocztowy = $dom->createAttribute('kodPocztowy');
        $kodPocztowy->value = $this->data['delivery_address']['postal_code'];
        $telefon = $dom->createAttribute('telefon');
        $telefon->value = $this->data['delivery_address']['phone'];
        $email = $dom->createAttribute('email');
        $email->value = $this->data['delivery_address']['email'];
        $kraj = $dom->createAttribute('kraj');
        $kraj->value = 'Polska';
        $osobaKontaktowa = $dom->createAttribute('osobaKontaktowa');
        $osobaKontaktowa->value = $this->data['pickup_address']['firstname'] . ' ' . $this->data['pickup_address']['lastname'];
        $osobaKontaktowa2 = $dom->createAttribute('osobaKontaktowa');
        $osobaKontaktowa2->value = $this->data['delivery_address']['firstname'] . ' ' . $this->data['delivery_address']['lastname'];

        $addressElement->appendChild($nazwa);
        $addressElement->appendChild($nazwa2);
        $addressElement->appendChild($ulica);
        $addressElement->appendChild($numerDomu);
        $addressElement->appendChild($miejscowosc);
        $addressElement->appendChild($kodPocztowy);
        $addressElement->appendChild($telefon);
        $addressElement->appendChild($email);
        $addressElement->appendChild($kraj);
        $addressElement->appendChild($osobaKontaktowa);
    }
}