<?php

namespace App\Enums;

enum OrderPaymentsEnum
{
    const REBOOKED_TYPE_OUT = 'przeksiegowanie na inna oferte';
    const REBOOKED_TYPE_IN = 'przeksiegowanie z innej oferty';
    const DECLARED_FROM_ALLEGRO = 'deklarowana z allegro';
    const KWON_STATUS = 'wartość towaru oferty niewyjechanej';

    const INVOICE_BUYING_OPERATION_TYPE = 'Wpłata/wypłata bankowa - związana z fakturą zakupową';
}
