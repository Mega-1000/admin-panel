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
    const JAS = 'JAS';
    const GLS = 'GLS';
    const ODBIOR_OSOBISTY = 'ODBIOR_OSOBISTY';
    const GIELDA = 'GIELDA';
    const DB_SCHENKER = 'DB';

    /**
     * Delivery types labels
     */
    const DELIVERY_TYPE_LABELS = [
        self::INPOST => 'Paczkomat',
        self::ALLEGRO_INPOST => 'ALLEGRO-INPOST',
        self::DPD => 'Dpd',
        self::APACZKA => 'Apaczka',
        self::POCZTEX => 'Pocztex',
        self::JAS => 'Jas',
        self::GLS => 'Gls',
        self::ODBIOR_OSOBISTY => 'Odbiór osobisty',
        self::GIELDA => 'Giełda',
        self::DB_SCHENKER => 'DB Schenker',
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
        self::DB_SCHENKER => [self::DB_SCHENKER],
    ];

    const DELIVERY_TYPE_TO_SEND_PACKAGE = [
        self::INPOST,
        self::APACZKA,
        self::DPD,
        self::POCZTEX,
        self::JAS,
        self::ALLEGRO_INPOST,
        self::GLS,
        self::DB_SCHENKER,
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
        self::DB_SCHENKER => 'DB Schenker',
        'all' => 'Wszystkie',
    ];
}
