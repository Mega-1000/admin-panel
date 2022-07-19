<?php

namespace App\Integrations\Artoit;

class PreTowar
{
    private $rodzaj;
    private $symbol;
    private $symbolDostawcy;
    private $nazwaDostawcy;
    private $symbolProducenta;
    private $nazwaProducenta;
    private $nazwa;
    private $cenaKartotekowaNetto;
    private $cenaNetto;
    private $JM;
    private $kodKreskowy;
    private $vat;
    private $PKWiU;
    private $opis;
    private $opisPelny;
    private $uwagi;
    private $adresWWW;
    private $wysokosc;
    private $dlugosc;
    private $szerokosc;
    private $waga;
    private $poleWlasne;

    /**
     * @param $rodzaj
     * @param $symbol
     * @param $symbolDostawcy
     * @param $nazwaDostawcy
     * @param $symbolProducenta
     * @param $nazwaProducenta
     * @param $nazwa
     * @param $cenaKartotekowaNetto
     * @param $cenaNetto
     * @param $JM
     * @param $kodKreskowy
     * @param $vat
     * @param $PKWiU
     * @param $opis
     * @param $opisPelny
     * @param $uwagi
     * @param $adresWWW
     * @param $wysokosc
     * @param $dlugosc
     * @param $szerokosc
     * @param $waga
     * @param $poleWlasne
     */
    public function __construct(
        ?ERodzajTowaru $rodzaj,
        ?string        $symbol,
        ?string        $symbolDostawcy,
        ?string        $nazwaDostawcy,
        ?string        $symbolProducenta,
        ?string        $nazwaProducenta,
        ?string        $nazwa,
        ?float         $cenaKartotekowaNetto,
        ?float         $cenaNetto,
        ?string        $JM,
        ?string        $kodKreskowy,
        ?string        $vat,
        ?string        $PKWiU,
        ?string        $opis,
        ?string        $opisPelny,
        ?string        $uwagi,
        ?string        $adresWWW,
        ?float         $wysokosc,
        ?float         $dlugosc,
        ?float         $szerokosc,
        ?float         $waga,
        string         $poleWlasne
    )
    {
        $this->rodzaj = $rodzaj;
        $this->symbol = $symbol;
        $this->symbolDostawcy = $symbolDostawcy;
        $this->nazwaDostawcy = $nazwaDostawcy;
        $this->symbolProducenta = $symbolProducenta;
        $this->nazwaProducenta = $nazwaProducenta;
        $this->nazwa = $nazwa;
        $this->cenaKartotekowaNetto = $cenaKartotekowaNetto;
        $this->cenaNetto = $cenaNetto;
        $this->JM = $JM;
        $this->kodKreskowy = $kodKreskowy;
        $this->vat = $vat;
        $this->PKWiU = $PKWiU;
        $this->opis = $opis;
        $this->opisPelny = $opisPelny;
        $this->uwagi = $uwagi;
        $this->adresWWW = $adresWWW;
        $this->wysokosc = $wysokosc;
        $this->dlugosc = $dlugosc;
        $this->szerokosc = $szerokosc;
        $this->waga = $waga;
        $this->poleWlasne = $poleWlasne;
    }

    /**
     * @return string|null
     */
    public function getRodzaj(): ?string
    {
        return $this->rodzaj;
    }

    /**
     * @return string|null
     */
    public function getSymbol(): ?string
    {
        return $this->symbol;
    }

    /**
     * @return string|null
     */
    public function getSymbolDostawcy(): ?string
    {
        return $this->symbolDostawcy;
    }

    /**
     * @return string|null
     */
    public function getNazwaDostawcy(): ?string
    {
        return $this->nazwaDostawcy;
    }

    /**
     * @return string|null
     */
    public function getSymbolProducenta(): ?string
    {
        return $this->symbolProducenta;
    }

    /**
     * @return string|null
     */
    public function getNazwaProducenta(): ?string
    {
        return $this->nazwaProducenta;
    }

    /**
     * @return string|null
     */
    public function getNazwa(): ?string
    {
        return $this->nazwa;
    }

    /**
     * @return float|null
     */
    public function getCenaKartotekowaNetto(): ?float
    {
        return $this->cenaKartotekowaNetto;
    }

    /**
     * @return float|null
     */
    public function getCenaNetto(): ?float
    {
        return $this->cenaNetto;
    }

    /**
     * @return string|null
     */
    public function getJM(): ?string
    {
        return $this->JM;
    }

    /**
     * @return string|null
     */
    public function getKodKreskowy(): ?string
    {
        return $this->kodKreskowy;
    }

    /**
     * @return string|null
     */
    public function getVat(): ?string
    {
        return $this->vat;
    }

    /**
     * @return string|null
     */
    public function getPKWiU(): ?string
    {
        return $this->PKWiU;
    }

    /**
     * @return string|null
     */
    public function getOpis(): ?string
    {
        return $this->opis;
    }

    /**
     * @return string|null
     */
    public function getOpisPelny(): ?string
    {
        return $this->opisPelny;
    }

    /**
     * @return string|null
     */
    public function getUwagi(): ?string
    {
        return $this->uwagi;
    }

    /**
     * @return string|null
     */
    public function getAdresWWW(): ?string
    {
        return $this->adresWWW;
    }

    /**
     * @return float|null
     */
    public function getWysokosc(): ?float
    {
        return $this->wysokosc;
    }

    /**
     * @return float|null
     */
    public function getDlugosc(): ?float
    {
        return $this->dlugosc;
    }

    /**
     * @return float|null
     */
    public function getSzerokosc(): ?float
    {
        return $this->szerokosc;
    }

    /**
     * @return float|null
     */
    public function getWaga(): ?float
    {
        return $this->waga;
    }

    /**
     * @return string
     */
    public function getPoleWlasne(): string
    {
        return $this->poleWlasne;
    }
}
