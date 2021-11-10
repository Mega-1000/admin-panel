<?php
declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class CourierName extends Enum
{
    const INPOST = 'INPOST';
    const ALLEGRO_INPOST = 'ALLEGRO-INPOST';
    const PACZKOMAT = 'PACZKOMAT';
    const DPD = 'DPD';
    const APACZKA = 'APACZKA';
    const POCZTEX = 'POCZTEX';
    const KURIER48 = 'KURIER48';
    const JAS = 'JAS';
    const GLS = 'GLS';
    const ODBIOR_OSOBISTY = 'ODBIOR_OSOBISTY';
    const GIELDA = 'GIELDA';

    /**
     * Delivery types labels
     */
    const DELIVERY_TYPE_LABELS = [
        self::INPOST => 'Paczkomat',
        self::ALLEGRO_INPOST => 'Paczkomat',
        self::DPD => 'Dpd',
        self::APACZKA => 'Apaczka',
        self::POCZTEX => 'Pocztex',
        self::JAS => 'Jas',
        self::GLS => 'Gls',
        self::ODBIOR_OSOBISTY => 'Odbiór osobisty',
        self::GIELDA => 'Giełda',
    ];

    /**
     * Deliveries types for task grouping
     */
    const DELIVERY_TYPE_FOR_TASKS = [
        self::GLS => [self::GLS],
        self::DPD => [self::DPD],
        self::POCZTEX => [self::POCZTEX],
        self::INPOST => [self::INPOST, self::ALLEGRO_INPOST],
        self::ODBIOR_OSOBISTY => [self::ODBIOR_OSOBISTY],
        self::GIELDA => [self::GIELDA],
    ];

    /**
     * Delivery types labels
     */
    const NAMES_FOR_DAY_CLOSE = [
        self::PACZKOMAT => 'Paczkomat',
        self::DPD => 'Dpd',
        self::APACZKA => 'Apaczka',
        self::POCZTEX => 'Pocztex',
        self::JAS => 'Jas',
        self::GLS => 'Gls',
        self::GIELDA => 'Giełda',
        'all' => 'Wszystkie',
    ];
}
