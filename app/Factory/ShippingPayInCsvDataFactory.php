<?php

namespace App\Factory;

use App\DTO\ImportPayIn\ShippingPayInCsvDataDTO;

class ShippingPayInCsvDataFactory
{
    // create DTO from array
    public function createFromArray(array $data): ShippingPayInCsvDataDTO
    {
        return new ShippingPayInCsvDataDTO(
            $data['nr_faktury_do_ktorej_dany_lp_zostal_przydzielony'],
            $data['wartosc_pobrania'],
        );
    }
}
