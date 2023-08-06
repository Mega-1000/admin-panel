<?php

namespace App\Factory;

use App\Enums\CourierName;
use App\Helpers\DpdCourier;
use App\Helpers\GlsCourier;
use App\Helpers\InpostCourier;
use App\Helpers\JasCourier;
use App\Helpers\PocztexCourier;
use InvalidArgumentException;

class CourierFactory
{
    public static function create(string $courierName): DpdCourier|InpostCourier
    {
        return match ($courierName) {
            CourierName::INPOST, CourierName::ALLEGRO_INPOST => new InpostCourier(),
            CourierName::DPD                                 => new DpdCourier(),
            CourierName::GLS                                 => new GlsCourier(),
            CourierName::JAS                                 => new JasCourier(),
            CourierName::POCZTEX                             => new PocztexCourier(),
            default                                          => throw new InvalidArgumentException("Invalid courier name: $courierName"),
        };
    }
}
