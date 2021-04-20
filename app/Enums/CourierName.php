<?php
declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CourierName extends Enum
{
    const INPOST = 'INPOST';
    const ALLEGRO_INPOST = 'ALLEGRO-INPOST';
    const DPD = 'DPD';
    const APACZKA = 'APACZKA';
    const POCZTEX = 'POCZTEX';
    const JAS = 'JAS';
    const GLS = 'GLS';
    const ODBIOR_OSOBISTY = 'ODBIOR_OSOBISTY';

    /**
     * Delivery types labels
     */
    const DELIVERY_TYPE_LABELS = [
        self::INPOST => 'Paczkomaty',
        self::ALLEGRO_INPOST => 'Paczkomaty',
        self::DPD => 'Dpd',
        self::APACZKA => 'Apaczka',
        self::POCZTEX => 'Pocztex',
        self::JAS => 'Jas',
        self::GLS => 'Gls',
        self::ODBIOR_OSOBISTY => 'Odbiór osobisty',
    ];

    /**
     * Deliveries types for task grouping
     */
    const DELIVERY_TYPE_FOR_TASKS = [
        self::GLS => [self::GLS],
        self::DPD => [self::DPD],
        self::POCZTEX => [self::POCZTEX],
        self::INPOST => [self::POCZTEX, self::ALLEGRO_INPOST],
        self::ODBIOR_OSOBISTY => [self::ODBIOR_OSOBISTY],
    ];
}
