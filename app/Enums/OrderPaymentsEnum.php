<?php

namespace App\Enums;

enum OrderPaymentsEnum
{
    const REBOOKED_TYPE_OUT = 'przeksiegowanie na inna oferte';
    const REBOOKED_TYPE_IN = 'przeksiegowanie z innej oferty';
    const DECLARED_FROM_ALLEGRO = 'deklarowana z allegro';
}
