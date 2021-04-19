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
     * Opisy firm kurierskich/ sposób dostawy
     */
    const DELIVERY_TYPE_LABELS = [
        self::INPOST => 'Inpost',
        self::ALLEGRO_INPOST => 'inpost',
        self::DPD => 'Dpd',
        self::APACZKA => 'Apaczka',
        self::POCZTEX => 'Pocztex',
        self::JAS => 'Jas',
        self::GLS => 'GLS',
        self::ODBIOR_OSOBISTY => 'Odbiór osobisty',
    ];

    /**
     * Typy dostaw do pobierania tasków
     */
    const DELIVERY_TYPE_FOR_TASKS = [
        self::GLS => [self::GLS],
        self::DPD => [self::DPD],
        self::POCZTEX => [self::POCZTEX],
        self::INPOST => [self::POCZTEX, self::ALLEGRO_INPOST],
        self::ODBIOR_OSOBISTY => [self::ODBIOR_OSOBISTY],
    ];
}
