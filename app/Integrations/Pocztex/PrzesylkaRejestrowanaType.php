<?php


namespace App\Integrations\Pocztex;


class PrzesylkaRejestrowanaType extends PrzesylkaType
{
    /**
     * Numer nadania przesyłki. Należy podać tutaj właściwy numer nadania otrzymany
     * z Poczty Polskiej S.A. Numery nadania różnią się budową w zależności od rodzaju usługi.
     * Pole NIE jest wymagane.
     *
     * @var string|null
     */
    protected $numerNadania;

    /**
     * Element klasy adresType –zawiera informacje o odbiorcy przesyłki. Musi wystąpić dokładnie 1 raz.
     *
     * @var adresType
     */
    protected $adres;

    /**
     * Atrybut opcjonalny, przeznaczony do uzupełniania dla Klientów nadających przesyłki na
     * zasadach specjalnych. Należy przekazać element zgodny z interfejsem sygnaturaType
     *
     * @var string
     */
    protected $sygnatura;

    /**
     * Atrybut opcjonalny, przeznaczony do uzupełniania dla Klientów nadających przesyłki na
     * zasadach specjalnych. Należy przekazać element zgodny z interfejsem terminType
     *
     * @var string|null
     */
    protected $terminType;

    /**
     * Atrybut opcjonalny, przeznaczony do uzupełniania dla Klientów nadających przesyłki na
     * zasadach specjalnych. Należy przekazać element zgodny z interfejsem rodzajType.
     *
     * @var string|null
     */
    protected $rodzaj;
}