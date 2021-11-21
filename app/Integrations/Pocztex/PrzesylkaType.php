<?php

namespace App\Integrations\Pocztex;

abstract class PrzesylkaType
{
    /**
     * Planowana data nadania przesyłki.
     *
     * @var string
     */
    protected $planowanaDataNadania;

    /**
     * Identyfikator guid, pole to jest wymagane. Możliwe jest wykorzystanie go do
     * celów powiązania nadawanych informacji o przesyłkach z danymi w swoim
     * systemie. Pole to służy do powiązania błędów zwracanych z nadawanymi
     * przesyłkami.
     * http://pl.wikipedia.org/wiki/Globally_Unique_Identifier. Zalecamy
     * przekazywanie go jako ciągu niesformatowanego (^[A-F0-9]{32}$)
     *
     * @var string
     */
    protected $guid;

    /**
     * Opis przesyłki
     *
     * @var string
     */
    protected $opis;

    /**
     * @var uiszczaOplateType
     */
    protected $oplacaOdbiorca;
}