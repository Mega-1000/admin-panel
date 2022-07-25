<?php

namespace App\Integrations\Artoit;

/**
 *
 */
class PrePozycja
{
    /**
     * @var PreTowar
     */
    public $towar;

    /**
     * @var float
     */
    public $rabatProcent;

    /**
     * @var float
     */
    public $cenaNettoPrzedRabatem;

    /**
     * @var float
     */
    public $cenaNettoPoRabacie;

    /**
     * @var float
     */
    public $cenaBruttoPrzedRabatem;

    /**
     * @var float
     */
    public $cenaBruttoPoRabacie;

    /**
     * @var int
     */
    public $ilosc;

    /**
     * @var float
     */
    public $vat;

    /**
     * @var string
     */
    public $opisPozycji;

    /**
     * @var string
     */
    public $kodDostawy;

    /**
     * @var float
     */
    public $wartoscCalejPozycjiNettoZRabatem;

    /**
     * @var float
     */
    public $wartoscCalejPozycjiBruttoZRabatem;

    /**
     * @var float
     */
    public $wartoscCalejPozycjiNetto;

    /**
     * @var float
     */
    public $wartoscCalejPozycjiBrutto;

    /**
     * @param PreTowar $towar
     *
     * @return PrePozycja
     */
    public function setTowar(PreTowar $towar): PrePozycja
    {
        $this->towar = $towar;
        return $this;
    }

    /**
     * @param float $rabatProcent
     *
     * @return PrePozycja
     */
    public function setRabatProcent(float $rabatProcent): PrePozycja
    {
        $this->rabatProcent = $rabatProcent;
        return $this;
    }

    /**
     * @param float $cenaNettoPrzedRabatem
     *
     * @return PrePozycja
     */
    public function setCenaNettoPrzedRabatem(float $cenaNettoPrzedRabatem): PrePozycja
    {
        $this->cenaNettoPrzedRabatem = $cenaNettoPrzedRabatem;
        return $this;
    }

    /**
     * @param float $cenaNettoPoRabacie
     *
     * @return PrePozycja
     */
    public function setCenaNettoPoRabacie(float $cenaNettoPoRabacie): PrePozycja
    {
        $this->cenaNettoPoRabacie = $cenaNettoPoRabacie;
        return $this;
    }

    /**
     * @param float $cenaBruttoPrzedRabatem
     *
     * @return PrePozycja
     */
    public function setCenaBruttoPrzedRabatem(float $cenaBruttoPrzedRabatem): PrePozycja
    {
        $this->cenaBruttoPrzedRabatem = $cenaBruttoPrzedRabatem;
        return $this;
    }

    /**
     * @param float $cenaBruttoPoRabacie
     *
     * @return PrePozycja
     */
    public function setCenaBruttoPoRabacie(float $cenaBruttoPoRabacie): PrePozycja
    {
        $this->cenaBruttoPoRabacie = $cenaBruttoPoRabacie;
        return $this;
    }

    /**
     * @param int $ilosc
     *
     * @return PrePozycja
     */
    public function setIlosc(int $ilosc): PrePozycja
    {
        $this->ilosc = $ilosc;
        return $this;
    }

    /**
     * @param float $vat
     *
     * @return PrePozycja
     */
    public function setVat(float $vat): PrePozycja
    {
        $this->vat = $vat;
        return $this;
    }

    /**
     * @param string $opisPozycji
     *
     * @return PrePozycja
     */
    public function setOpisPozycji(string $opisPozycji): PrePozycja
    {
        $this->opisPozycji = $opisPozycji;
        return $this;
    }

    /**
     * @param string $kodDostawy
     *
     * @return PrePozycja
     */
    public function setKodDostawy(string $kodDostawy): PrePozycja
    {
        $this->kodDostawy = $kodDostawy;
        return $this;
    }

    /**
     * @param float $wartoscCalejPozycjiNettoZRabatem
     *
     * @return PrePozycja
     */
    public function setWartoscCalejPozycjiNettoZRabatem(float $wartoscCalejPozycjiNettoZRabatem): PrePozycja
    {
        $this->wartoscCalejPozycjiNettoZRabatem = $wartoscCalejPozycjiNettoZRabatem;
        return $this;
    }

    /**
     * @param float $wartoscCalejPozycjiBruttoZRabatem
     *
     * @return PrePozycja
     */
    public function setWartoscCalejPozycjiBruttoZRabatem(float $wartoscCalejPozycjiBruttoZRabatem): PrePozycja
    {
        $this->wartoscCalejPozycjiBruttoZRabatem = $wartoscCalejPozycjiBruttoZRabatem;
        return $this;
    }

    /**
     * @param float $wartoscCalejPozycjiNetto
     *
     * @return PrePozycja
     */
    public function setWartoscCalejPozycjiNetto(float $wartoscCalejPozycjiNetto): PrePozycja
    {
        $this->wartoscCalejPozycjiNetto = $wartoscCalejPozycjiNetto;
        return $this;
    }

    /**
     * @param float $wartoscCalejPozycjiBrutto
     *
     * @return PrePozycja
     */
    public function setWartoscCalejPozycjiBrutto(float $wartoscCalejPozycjiBrutto): PrePozycja
    {
        $this->wartoscCalejPozycjiBrutto = $wartoscCalejPozycjiBrutto;
        return $this;
    }
}
