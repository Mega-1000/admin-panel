<?php

namespace App\Integrations\Artoit;

class PrePozycja
{
    private $towar;
    private $rabatProcent;
    private $cenaNettoPrzedRabatem;
    private $cenaNettoPoRabacie;
    private $cenaBruttoPrzedRabatem;
    private $cenaBruttoPoRabacie;
    private $ilosc;
    private $vat;
    private $opisPozycji;
    private $kodDostawy;
    private $wartoscCalejPozycjiNettoZRabatem;
    private $wartoscCalejPozycjiBruttoZRabatem;
    private $wartoscCalejPozycjiNetto;
    private $wartoscCalejPozycjiBrutto;

    /**
     * @param PreTowar    $towar
     * @param float|null  $rabatProcent
     * @param float|null  $cenaNettoPrzedRabatem
     * @param float|null  $cenaNettoPoRabacie
     * @param float|null  $cenaBruttoPrzedRabatem
     * @param float|null  $cenaBruttoPoRabacie
     * @param float|null  $ilosc
     * @param float|null  $vat
     * @param string|null $opisPozycji
     * @param string|null $kodDostawy
     * @param float|null  $wartoscCalejPozycjiNettoZRabatem
     * @param float|null  $wartoscCalejPozycjiBruttoZRabatem
     * @param float|null  $wartoscCalejPozycjiNetto
     * @param float|null  $wartoscCalejPozycjiBrutto
     */
    public function __construct(
        PreTowar $towar,
        ?float   $rabatProcent,
        ?float   $cenaNettoPrzedRabatem,
        ?float   $cenaNettoPoRabacie,
        ?float   $cenaBruttoPrzedRabatem,
        ?float   $cenaBruttoPoRabacie,
        ?float   $ilosc,
        ?float   $vat,
        ?string  $opisPozycji,
        ?string  $kodDostawy,
        ?float   $wartoscCalejPozycjiNettoZRabatem,
        ?float   $wartoscCalejPozycjiBruttoZRabatem,
        ?float   $wartoscCalejPozycjiNetto,
        ?float   $wartoscCalejPozycjiBrutto
    )
    {
        $this->towar = $towar;
        $this->rabatProcent = $rabatProcent;
        $this->cenaNettoPrzedRabatem = $cenaNettoPrzedRabatem;
        $this->cenaNettoPoRabacie = $cenaNettoPoRabacie;
        $this->cenaBruttoPrzedRabatem = $cenaBruttoPrzedRabatem;
        $this->cenaBruttoPoRabacie = $cenaBruttoPoRabacie;
        $this->ilosc = $ilosc;
        $this->vat = $vat;
        $this->opisPozycji = $opisPozycji;
        $this->kodDostawy = $kodDostawy;
        $this->wartoscCalejPozycjiNettoZRabatem = $wartoscCalejPozycjiNettoZRabatem;
        $this->wartoscCalejPozycjiBruttoZRabatem = $wartoscCalejPozycjiBruttoZRabatem;
        $this->wartoscCalejPozycjiNetto = $wartoscCalejPozycjiNetto;
        $this->wartoscCalejPozycjiBrutto = $wartoscCalejPozycjiBrutto;
    }

    /**
     * @return PreTowar
     */
    public function getTowar(): PreTowar
    {
        return $this->towar;
    }

    /**
     * @return float|null
     */
    public function getRabatProcent(): ?float
    {
        return $this->rabatProcent;
    }

    /**
     * @return float|null
     */
    public function getCenaNettoPrzedRabatem(): ?float
    {
        return $this->cenaNettoPrzedRabatem;
    }

    /**
     * @return float|null
     */
    public function getCenaNettoPoRabacie(): ?float
    {
        return $this->cenaNettoPoRabacie;
    }

    /**
     * @return float|null
     */
    public function getCenaBruttoPrzedRabatem(): ?float
    {
        return $this->cenaBruttoPrzedRabatem;
    }

    /**
     * @return float|null
     */
    public function getCenaBruttoPoRabacie(): ?float
    {
        return $this->cenaBruttoPoRabacie;
    }

    /**
     * @return float|null
     */
    public function getIlosc(): ?float
    {
        return $this->ilosc;
    }

    /**
     * @return float|null
     */
    public function getVat(): ?float
    {
        return $this->vat;
    }

    /**
     * @return string|null
     */
    public function getOpisPozycji(): ?string
    {
        return $this->opisPozycji;
    }

    /**
     * @return string|null
     */
    public function getKodDostawy(): ?string
    {
        return $this->kodDostawy;
    }

    /**
     * @return float|null
     */
    public function getWartoscCalejPozycjiNettoZRabatem(): ?float
    {
        return $this->wartoscCalejPozycjiNettoZRabatem;
    }

    /**
     * @return float|null
     */
    public function getWartoscCalejPozycjiBruttoZRabatem(): ?float
    {
        return $this->wartoscCalejPozycjiBruttoZRabatem;
    }

    /**
     * @return float|null
     */
    public function getWartoscCalejPozycjiNetto(): ?float
    {
        return $this->wartoscCalejPozycjiNetto;
    }

    /**
     * @return float|null
     */
    public function getWartoscCalejPozycjiBrutto(): ?float
    {
        return $this->wartoscCalejPozycjiBrutto;
    }
}
