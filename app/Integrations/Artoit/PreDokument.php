<?php

namespace App\Integrations\Artoit;

use DateTime;

class PreDokument
{
    private $adresDostawy;
    private $klient;
    private $uslugaTransportu;
    private $uslugaTransportuCenaNetto;
    private $uslugaTransportuCenaBrutto;
    private $numer;
    private $numerPelny;
    private $numerZewnetrzny;
    private $numerZewnetrzny2;
    private $dataUtworzenia;
    private $dataDostawy;
    /// Data platnosci dokumentu
    private $terminPlatnosci;
    private $produkty;

    private $uwagi;
    private $rodzajPlatnosci;
    private $waluta;
    private $wartoscPoRabacieNetto;
    private $wartoscPoRabacieBrutto;
    private $wartoscNetto;
    private $wartoscBrutto;
    private $wartoscWplacona;
    private $typDokumentu;
    private $statusDokumentuWERP;

    private $kategoria;
    private $magazyn;
    /// Do obsługi MM
    private $magazynDo;

    /**
     * @param PreAdres                $adresDostawy
     * @param PreKlient               $klient
     * @param string|null             $uslugaTransportu
     * @param float|null              $uslugaTransportuCenaNetto
     * @param float|null              $uslugaTransportuCenaBrutto
     * @param int|null                $numer
     * @param string|null             $numerPelny
     * @param string|null             $numerZewnetrzny
     * @param string|null             $numerZewnetrzny2
     * @param DateTime|null          $dataUtworzenia
     * @param DateTime|null          $dataDostawy
     * @param DateTime|null          $terminPlatnosci
     * @param array                   $produkty
     * @param string|null             $uwagi
     * @param string|null             $rodzajPlatnosci
     * @param string|null             $waluta
     * @param float|null              $wartoscPoRabacieNetto
     * @param float|null              $wartoscPoRabacieBrutto
     * @param float|null              $wartoscNetto
     * @param float|null              $wartoscBrutto
     * @param float|null              $wartoscWplacona
     * @param ETypDokumentu_HandloMag $typDokumentu
     * @param string|null             $statusDokumentuWERP
     * @param string|null             $kategoria
     * @param string|null             $magazyn
     * @param string|null             $magazynDo
     */
    public function __construct(
        PreAdres                $adresDostawy,
        PreKlient               $klient,
        ?string                 $uslugaTransportu,
        ?float                  $uslugaTransportuCenaNetto,
        ?float                  $uslugaTransportuCenaBrutto,
        ?int                    $numer,
        ?string                 $numerPelny,
        ?string                 $numerZewnetrzny,
        ?string                 $numerZewnetrzny2,
        ?DateTime               $dataUtworzenia,
        ?DateTime               $dataDostawy,
        ?DateTime               $terminPlatnosci,
        array                   $produkty,
        ?string                 $uwagi,
        ?string                 $rodzajPlatnosci,
        ?string                 $waluta,
        ?float                  $wartoscPoRabacieNetto,
        ?float                  $wartoscPoRabacieBrutto,
        ?float                  $wartoscNetto,
        ?float                  $wartoscBrutto,
        ?float                  $wartoscWplacona,
        ETypDokumentu_HandloMag $typDokumentu,
        ?string                 $statusDokumentuWERP,
        ?string                 $kategoria,
        ?string                 $magazyn,
        ?string                 $magazynDo
    )
    {
        $this->adresDostawy = $adresDostawy;
        $this->klient = $klient;
        $this->uslugaTransportu = $uslugaTransportu;
        $this->uslugaTransportuCenaNetto = $uslugaTransportuCenaNetto;
        $this->uslugaTransportuCenaBrutto = $uslugaTransportuCenaBrutto;
        $this->numer = $numer;
        $this->numerPelny = $numerPelny;
        $this->numerZewnetrzny = $numerZewnetrzny;
        $this->numerZewnetrzny2 = $numerZewnetrzny2;
        $this->dataUtworzenia = $dataUtworzenia;
        $this->dataDostawy = $dataDostawy;
        $this->terminPlatnosci = $terminPlatnosci;
        $this->produkty = $produkty;
        $this->uwagi = $uwagi;
        $this->rodzajPlatnosci = $rodzajPlatnosci;
        $this->waluta = $waluta;
        $this->wartoscPoRabacieNetto = $wartoscPoRabacieNetto;
        $this->wartoscPoRabacieBrutto = $wartoscPoRabacieBrutto;
        $this->wartoscNetto = $wartoscNetto;
        $this->wartoscBrutto = $wartoscBrutto;
        $this->wartoscWplacona = $wartoscWplacona;
        $this->typDokumentu = $typDokumentu;
        $this->statusDokumentuWERP = $statusDokumentuWERP;
        $this->kategoria = $kategoria;
        $this->magazyn = $magazyn;
        $this->magazynDo = $magazynDo;
    }

    /**
     * @return PreAdres
     */
    public function getAdresDostawy(): PreAdres
    {
        return $this->adresDostawy;
    }

    /**
     * @return PreKlient
     */
    public function getKlient(): PreKlient
    {
        return $this->klient;
    }

    /**
     * @return string|null
     */
    public function getUslugaTransportu(): ?string
    {
        return $this->uslugaTransportu;
    }

    /**
     * @return float|null
     */
    public function getUslugaTransportuCenaNetto(): ?float
    {
        return $this->uslugaTransportuCenaNetto;
    }

    /**
     * @return float|null
     */
    public function getUslugaTransportuCenaBrutto(): ?float
    {
        return $this->uslugaTransportuCenaBrutto;
    }

    /**
     * @return int|null
     */
    public function getNumer(): ?int
    {
        return $this->numer;
    }

    /**
     * @return string|null
     */
    public function getNumerPelny(): ?string
    {
        return $this->numerPelny;
    }

    /**
     * @return string|null
     */
    public function getNumerZewnetrzny(): ?string
    {
        return $this->numerZewnetrzny;
    }

    /**
     * @return string|null
     */
    public function getNumerZewnetrzny2(): ?string
    {
        return $this->numerZewnetrzny2;
    }

    /**
     * @return DateTime|null
     */
    public function getDataUtworzenia(): ?DateTime
    {
        return $this->dataUtworzenia;
    }

    /**
     * @return DateTime|null
     */
    public function getDataDostawy(): ?DateTime
    {
        return $this->dataDostawy;
    }

    /**
     * @return DateTime|null
     */
    public function getTerminPlatnosci(): ?DateTime
    {
        return $this->terminPlatnosci;
    }

    /**
     * @return array
     */
    public function getProdukty(): array
    {
        return $this->produkty;
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
    public function getRodzajPlatnosci(): ?string
    {
        return $this->rodzajPlatnosci;
    }

    /**
     * @return string|null
     */
    public function getWaluta(): ?string
    {
        return $this->waluta;
    }

    /**
     * @return float|null
     */
    public function getWartoscPoRabacieNetto(): ?float
    {
        return $this->wartoscPoRabacieNetto;
    }

    /**
     * @return float|null
     */
    public function getWartoscPoRabacieBrutto(): ?float
    {
        return $this->wartoscPoRabacieBrutto;
    }

    /**
     * @return float|null
     */
    public function getWartoscNetto(): ?float
    {
        return $this->wartoscNetto;
    }

    /**
     * @return float|null
     */
    public function getWartoscBrutto(): ?float
    {
        return $this->wartoscBrutto;
    }

    /**
     * @return float|null
     */
    public function getWartoscWplacona(): ?float
    {
        return $this->wartoscWplacona;
    }

    /**
     * @return ETypDokumentu_HandloMag
     */
    public function getTypDokumentu(): ETypDokumentu_HandloMag
    {
        return $this->typDokumentu;
    }

    /**
     * @return string|null
     */
    public function getStatusDokumentuWERP(): ?string
    {
        return $this->statusDokumentuWERP;
    }

    /**
     * @return string|null
     */
    public function getKategoria(): ?string
    {
        return $this->kategoria;
    }

    /**
     * @return string|null
     */
    public function getMagazyn(): ?string
    {
        return $this->magazyn;
    }

    /**
     * @return string|null
     */
    public function getMagazynDo(): ?string
    {
        return $this->magazynDo;
    }
}
