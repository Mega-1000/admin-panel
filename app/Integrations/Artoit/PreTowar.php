<?php

namespace App\Integrations\Artoit;

/**
 * Pre towar class
 */
class PreTowar
{
    /**
     * @var string
     */
    public $rodzaj;

    /**
     * @var string
     */
    public $symbol;

    /**
     * @var string
     */
    public $symbolDostawcy;

    /**
     * @var string
     */
    public $nazwaDostawcy;

    /**
     * @var string
     */
    public $symbolProducenta;

    /**
     * @var string
     */
    public $nazwaProducenta;

    /**
     * @var string
     */
    public $nazwa;

    /**
     * @var float
     */
    public $cenaKartotekowaNetto;

    /**
     * @var float
     */
    public $cenaNetto;

    /**
     * @var string
     */
    public $JM;

    /**
     * @var string
     */
    public $kodKreskowy;

    /**
     * @var string
     */
    public $vat;

    /**
     * @var string
     */
    public $PKWiU;

    /**
     * @var string
     */
    public $opis;

    /**
     * @var string
     */
    public $opisPelny;

    /**
     * @var string
     */
    public $uwagi;

    /**
     * @var string
     */
    public $adresWWW;

    /**
     * @var string
     */
    public $symboleSkladnikow;

    /**
     * @var string
     */
    public $iloscSkladnikow;

    /**
     * @var array
     */
    public $zdjecia;

    /**
     * @var float
     */
    public $wysokosc;

    /**
     * @var float
     */
    public $dlugosc;

    /**
     * @var float
     */
    public $szerokosc;

    /**
     * @var float
     */
    public $waga;

    /**
     * @var string
     */
    public $poleWlasne;

    /**
     * @param string $rodzaj
     *
     * @return PreTowar
     */
    public function setRodzaj(string $rodzaj): PreTowar
    {
        $this->rodzaj = $rodzaj;
        return $this;
    }

    /**
     * @param string $symbol
     *
     * @return PreTowar
     */
    public function setSymbol(string $symbol): PreTowar
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @param string $symbolDostawcy
     *
     * @return PreTowar
     */
    public function setSymbolDostawcy(string $symbolDostawcy): PreTowar
    {
        $this->symbolDostawcy = $symbolDostawcy;
        return $this;
    }

    /**
     * @param string $nazwaDostawcy
     *
     * @return PreTowar
     */
    public function setNazwaDostawcy(string $nazwaDostawcy): PreTowar
    {
        $this->nazwaDostawcy = $nazwaDostawcy;
        return $this;
    }

    /**
     * @param string $symbolProducenta
     *
     * @return PreTowar
     */
    public function setSymbolProducenta(string $symbolProducenta): PreTowar
    {
        $this->symbolProducenta = $symbolProducenta;
        return $this;
    }

    /**
     * @param string $nazwaProducenta
     *
     * @return PreTowar
     */
    public function setNazwaProducenta(string $nazwaProducenta): PreTowar
    {
        $this->nazwaProducenta = $nazwaProducenta;
        return $this;
    }

    /**
     * @param string $nazwa
     *
     * @return PreTowar
     */
    public function setNazwa(string $nazwa): PreTowar
    {
        $this->nazwa = $nazwa;
        return $this;
    }

    /**
     * @param float $cenaKartotekowaNetto
     *
     * @return PreTowar
     */
    public function setCenaKartotekowaNetto(float $cenaKartotekowaNetto): PreTowar
    {
        $this->cenaKartotekowaNetto = $cenaKartotekowaNetto;
        return $this;
    }

    /**
     * @param float $cenaNetto
     *
     * @return PreTowar
     */
    public function setCenaNetto(float $cenaNetto): PreTowar
    {
        $this->cenaNetto = $cenaNetto;
        return $this;
    }

    /**
     * @param string $JM
     *
     * @return PreTowar
     */
    public function setJM(string $JM): PreTowar
    {
        $this->JM = $JM;
        return $this;
    }

    /**
     * @param string $kodKreskowy
     *
     * @return PreTowar
     */
    public function setKodKreskowy(string $kodKreskowy): PreTowar
    {
        $this->kodKreskowy = $kodKreskowy;
        return $this;
    }

    /**
     * @param string $vat
     *
     * @return PreTowar
     */
    public function setVat(string $vat): PreTowar
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @param string $PKWiU
     *
     * @return PreTowar
     */
    public function setPKWiU(string $PKWiU): PreTowar
    {
        $this->PKWiU = $PKWiU;
        return $this;
    }

    /**
     * @param string $opis
     *
     * @return PreTowar
     */
    public function setOpis(string $opis): PreTowar
    {
        $this->opis = $opis;
        return $this;
    }

    /**
     * @param string $opisPelny
     *
     * @return PreTowar
     */
    public function setOpisPelny(string $opisPelny): PreTowar
    {
        $this->opisPelny = $opisPelny;
        return $this;
    }

    /**
     * @param string $uwagi
     *
     * @return PreTowar
     */
    public function setUwagi(string $uwagi): PreTowar
    {
        $this->uwagi = $uwagi;
        return $this;
    }

    /**
     * @param string $adresWWW
     *
     * @return PreTowar
     */
    public function setAdresWWW(string $adresWWW): PreTowar
    {
        $this->adresWWW = $adresWWW;
        return $this;
    }

    /**
     * @param string $symboleSkladnikow
     *
     * @return PreTowar
     */
    public function setSymboleSkladnikow(string $symboleSkladnikow): PreTowar
    {
        $this->symboleSkladnikow = $symboleSkladnikow;
        return $this;
    }

    /**
     * @param string $iloscSkladnikow
     *
     * @return PreTowar
     */
    public function setIloscSkladnikow(string $iloscSkladnikow): PreTowar
    {
        $this->iloscSkladnikow = $iloscSkladnikow;
        return $this;
    }

    /**
     * @param array $zdjecia
     *
     * @return PreTowar
     */
    public function setZdjecia(array $zdjecia): PreTowar
    {
        $this->zdjecia = $zdjecia;
        return $this;
    }

    /**
     * @param float $wysokosc
     *
     * @return PreTowar
     */
    public function setWysokosc(float $wysokosc): PreTowar
    {
        $this->wysokosc = $wysokosc;
        return $this;
    }

    /**
     * @param float $dlugosc
     *
     * @return PreTowar
     */
    public function setDlugosc(float $dlugosc): PreTowar
    {
        $this->dlugosc = $dlugosc;
        return $this;
    }

    /**
     * @param float $szerokosc
     *
     * @return PreTowar
     */
    public function setSzerokosc(float $szerokosc): PreTowar
    {
        $this->szerokosc = $szerokosc;
        return $this;
    }

    /**
     * @param float $waga
     *
     * @return PreTowar
     */
    public function setWaga(float $waga): PreTowar
    {
        $this->waga = $waga;
        return $this;
    }

    /**
     * @param string $poleWlasne
     *
     * @return PreTowar
     */
    public function setPoleWlasne(string $poleWlasne): PreTowar
    {
        $this->poleWlasne = $poleWlasne;
        return $this;
    }
}
