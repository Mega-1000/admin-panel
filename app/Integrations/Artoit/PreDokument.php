<?php

namespace App\Integrations\Artoit;

use DateTime;

/**
 * PreDokument klas.
 */
class PreDokument
{
    /**
     * @var PreKlient
     */
    public $klient;

    /**
     * @var string
     */
    public $uslugaTransportu;

    /**
     * @var float
     */
    public $uslugaTransportuCenaNetto;

    /**
     * @var float
     */
    public $uslugaTransportuCenaBrutto;

    /**
     * @var int
     */
    public $numer;

    /**
     * @var string
     */
    public $numerPelny;

    /**
     * @var string
     */
    public $numerZewnetrzny;

    /**
     * @var string
     */
    public $numerZewnetrzny2;

    /**
     * @var DateTime
     */
    public $dataUtworzenia;

    /**
     * @var DateTime
     */
    public $dataDostawy;

    /**
     * @var DateTime
     */
    public $terminPlatnosci;

    /**
     * @var array
     */
    public $produkty;

    /**
     * @var string
     */
    public $uwagi;

    /**
     * @var string
     */
    public $rodzajPlatnosci;

    /**
     * @var string
     */
    public $waluta;

    /**
     * @var float
     */
    public $wartoscPoRabacieNetto;

    /**
     * @var float
     */
    public $wartoscPoRabacieBrutto;

    /**
     * @var float
     */
    public $wartoscNetto;

    /**
     * @var float
     */
    public $wartoscBrutto;

    /**
     * @var float
     */
    public $wartoscWplacona;

    /**
     * @var ETypDokumentu_HandloMag
     */
    public $typDokumentu;

    /**
     * @var string
     */
    public $statusDokumentuWERP;

    /**
     * @var string
     */
    public $kategoria;

    /**
     * @var string
     */
    public $magazyn;

    /**
     * @var string
     */
    public $magazynDo;

    /**
     * @param mixed $klient
     *
     * @return PreDokument
     */
    public function setKlient(PreKlient $klient)
    {
        $this->klient = $klient;
        return $this;
    }

    /**
     * @param string $uslugaTransportu
     *
     * @return PreDokument
     */
    public function setUslugaTransportu(string $uslugaTransportu)
    {
        $this->uslugaTransportu = $uslugaTransportu;
        return $this;
    }

    /**
     * @param float $uslugaTransportuCenaNetto
     *
     * @return PreDokument
     */
    public function setUslugaTransportuCenaNetto(float $uslugaTransportuCenaNetto)
    {
        $this->uslugaTransportuCenaNetto = $uslugaTransportuCenaNetto;
        return $this;
    }

    /**
     * @param float $uslugaTransportuCenaBrutto
     *
     * @return PreDokument
     */
    public function setUslugaTransportuCenaBrutto(float $uslugaTransportuCenaBrutto)
    {
        $this->uslugaTransportuCenaBrutto = $uslugaTransportuCenaBrutto;
        return $this;
    }

    /**
     * @param string $numer
     *
     * @return PreDokument
     */
    public function setNumer(string $numer)
    {
        $this->numer = $numer;
        return $this;
    }

    /**
     * @param string $numerPelny
     *
     * @return PreDokument
     */
    public function setNumerPelny(string $numerPelny)
    {
        $this->numerPelny = $numerPelny;
        return $this;
    }

    /**
     * @param string $numerZewnetrzny
     *
     * @return PreDokument
     */
    public function setNumerZewnetrzny(string $numerZewnetrzny)
    {
        $this->numerZewnetrzny = $numerZewnetrzny;
        return $this;
    }

    /**
     * @param string $numerZewnetrzny2
     *
     * @return PreDokument
     */
    public function setNumerZewnetrzny2(string $numerZewnetrzny2)
    {
        $this->numerZewnetrzny2 = $numerZewnetrzny2;
        return $this;
    }

    /**
     * @param string $dataUtworzenia
     *
     * @return PreDokument
     */
    public function setDataUtworzenia(string $dataUtworzenia)
    {
        $this->dataUtworzenia = $dataUtworzenia;
        return $this;
    }

    /**
     * @param string $dataDostawy
     *
     * @return PreDokument
     */
    public function setDataDostawy(string $dataDostawy)
    {
        $this->dataDostawy = $dataDostawy;
        return $this;
    }

    /**
     * @param string $terminPlatnosci
     *
     * @return PreDokument
     */
    public function setTerminPlatnosci(string $terminPlatnosci)
    {
        $this->terminPlatnosci = $terminPlatnosci;
        return $this;
    }

    /**
     * @param array $produkty
     *
     * @return PreDokument
     */
    public function setProdukty(array $produkty)
    {
        $this->produkty = $produkty;
        return $this;
    }

    /**
     * @param string $uwagi
     *
     * @return PreDokument
     */
    public function setUwagi(string $uwagi)
    {
        $this->uwagi = $uwagi;
        return $this;
    }

    /**
     * @param string $rodzajPlatnosci
     *
     * @return PreDokument
     */
    public function setRodzajPlatnosci(string $rodzajPlatnosci)
    {
        $this->rodzajPlatnosci = $rodzajPlatnosci;
        return $this;
    }

    /**
     * @param string $waluta
     *
     * @return PreDokument
     */
    public function setWaluta(string $waluta)
    {
        $this->waluta = $waluta;
        return $this;
    }

    /**
     * @param float $wartoscPoRabacieNetto
     *
     * @return PreDokument
     */
    public function setWartoscPoRabacieNetto(float $wartoscPoRabacieNetto)
    {
        $this->wartoscPoRabacieNetto = $wartoscPoRabacieNetto;
        return $this;
    }

    /**
     * @param float $wartoscPoRabacieBrutto
     *
     * @return PreDokument
     */
    public function setWartoscPoRabacieBrutto(float $wartoscPoRabacieBrutto)
    {
        $this->wartoscPoRabacieBrutto = $wartoscPoRabacieBrutto;
        return $this;
    }

    /**
     * @param float $wartoscNetto
     *
     * @return PreDokument
     */
    public function setWartoscNetto(float $wartoscNetto)
    {
        $this->wartoscNetto = $wartoscNetto;
        return $this;
    }

    /**
     * @param float $wartoscBrutto
     *
     * @return PreDokument
     */
    public function setWartoscBrutto(float $wartoscBrutto)
    {
        $this->wartoscBrutto = $wartoscBrutto;
        return $this;
    }

    /**
     * @param float $wartoscWplacona
     *
     * @return PreDokument
     */
    public function setWartoscWplacona(float $wartoscWplacona)
    {
        $this->wartoscWplacona = $wartoscWplacona;
        return $this;
    }

    /**
     * @param string $typDokumentu
     *
     * @return PreDokument
     */
    public function setTypDokumentu(string $typDokumentu)
    {
        $this->typDokumentu = $typDokumentu;
        return $this;
    }

    /**
     * @param string $statusDokumentuWERP
     *
     * @return PreDokument
     */
    public function setStatusDokumentuWERP(string $statusDokumentuWERP)
    {
        $this->statusDokumentuWERP = $statusDokumentuWERP;
        return $this;
    }

    /**
     * @param string $kategoria
     *
     * @return PreDokument
     */
    public function setKategoria(string $kategoria)
    {
        $this->kategoria = $kategoria;
        return $this;
    }

    /**
     * @param string $magazyn
     *
     * @return PreDokument
     */
    public function setMagazyn(string $magazyn)
    {
        $this->magazyn = $magazyn;
        return $this;
    }

    /**
     * @param string $magazynDo
     *
     * @return PreDokument
     */
    public function setMagazynDo(string $magazynDo)
    {
        $this->magazynDo = $magazynDo;
        return $this;
    }

    /**
     * @return array
     */
    public function getProdukty(): array
    {
        return $this->produkty ?? [];
    }
}
