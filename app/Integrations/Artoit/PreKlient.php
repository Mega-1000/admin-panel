<?php

namespace App\Integrations\Artoit;

class PreKlient
{

    private $typ;

    private $symbol;
    private $nazwa;
    private $nazwaPelna;
    private $osobaImie;
    private $sobaNazwisko;
    private $NIP;
    private $NIPUE;
    private $email;
    private $telefon;
    private $rodzajNaDok;
    private $nrRachunku;
    private $chceFV;
    private $adresGlowny;
    private $adresKoresp;

    /**
     * @param EPreKlientTyp         $typ
     * @param string|null           $symbol
     * @param string|null           $nazwa
     * @param string|null           $nazwaPelna
     * @param string|null           $osobaImie
     * @param string|null           $sobaNazwisko
     * @param string|null           $NIP
     * @param string|null           $NIPUE
     * @param string|null           $email
     * @param string|null           $telefon
     * @param EPreKlientRodzajNaDok $rodzajNaDok
     * @param string|null           $nrRachunku
     * @param string|null           $chceFV
     * @param PreAdres              $adresGlowny
     * @param PreAdres              $adresKoresp
     */
    public function __construct(
        EPreKlientTyp         $typ,
        ?string               $symbol,
        ?string               $nazwa,
        ?string               $nazwaPelna,
        ?string               $osobaImie,
        ?string               $sobaNazwisko,
        ?string               $NIP,
        ?string               $NIPUE,
        ?string               $email,
        ?string               $telefon,
        EPreKlientRodzajNaDok $rodzajNaDok,
        ?string               $nrRachunku,
        ?string               $chceFV,
        PreAdres              $adresGlowny,
        PreAdres              $adresKoresp
    )
    {
        $this->typ = $typ;
        $this->symbol = $symbol;
        $this->nazwa = $nazwa;
        $this->nazwaPelna = $nazwaPelna;
        $this->osobaImie = $osobaImie;
        $this->sobaNazwisko = $sobaNazwisko;
        $this->NIP = $NIP;
        $this->NIPUE = $NIPUE;
        $this->email = $email;
        $this->telefon = $telefon;
        $this->rodzajNaDok = $rodzajNaDok;
        $this->nrRachunku = $nrRachunku;
        $this->chceFV = $chceFV;
        $this->adresGlowny = $adresGlowny;
        $this->adresKoresp = $adresKoresp;
    }

    /**
     * @return string|null
     */
    public function getTyp(): ?string
    {
        return $this->typ;
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
    public function getNazwa(): ?string
    {
        return $this->nazwa;
    }

    /**
     * @return string|null
     */
    public function getNazwaPelna(): ?string
    {
        return $this->nazwaPelna;
    }

    /**
     * @return string|null
     */
    public function getOsobaImie(): ?string
    {
        return $this->osobaImie;
    }

    /**
     * @return string|null
     */
    public function getSobaNazwisko(): ?string
    {
        return $this->sobaNazwisko;
    }

    /**
     * @return string|null
     */
    public function getNIP(): ?string
    {
        return $this->NIP;
    }

    /**
     * @return string|null
     */
    public function getNIPUE(): ?string
    {
        return $this->NIPUE;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getTelefon(): ?string
    {
        return $this->telefon;
    }

    /**
     * @return string|null
     */
    public function getRodzajNaDok(): ?string
    {
        return $this->rodzajNaDok;
    }

    /**
     * @return string|null
     */
    public function getNrRachunku(): ?string
    {
        return $this->nrRachunku;
    }

    /**
     * @return string|null
     */
    public function getChceFV(): ?string
    {
        return $this->chceFV;
    }

    /**
     * @return string|null
     */
    public function getAdresGlowny(): ?string
    {
        return $this->adresGlowny;
    }

    /**
     * @return string|null
     */
    public function getAdresKoresp(): ?string
    {
        return $this->adresKoresp;
    }
}
