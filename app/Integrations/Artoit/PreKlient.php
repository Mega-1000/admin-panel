<?php

namespace App\Integrations\Artoit;

/**
 * PreKlient klass,
 */
class PreKlient
{
    /**
     * @var string
     */
    public $typ;

    /**
     * @var string
     */
    public $symbol;

    /**
     * @var string
     */
    public $nazwa;

    /**
     * @var string
     */
    public $nazwaPelna;

    /**
     * @var string
     */
    public $osobaImie;

    /**
     * @var string
     */
    public $osobaNazwisko;

    /**
     * @var string
     */
    public $NIP;

    /**
     * @var string
     */
    public $NIPUE;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $telefon;

    /**
     * @var string
     */
    public $rodzajNaDok;

    /**
     * @var  string
     */
    public $nrRachunku;

    /**
     * @var boolean
     */
    public $chceFV;

    /**
     * @var PreAdres
     */
    public $adresGlowny;

    /**
     * @param string $typ
     *
     * @return PreKlient
     */
    public function setTyp(string $typ): PreKlient
    {
        $this->typ = $typ;
        return $this;
    }

    /**
     * @param string $symbol
     *
     * @return PreKlient
     */
    public function setSymbol(string $symbol): PreKlient
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * @param string $nazwa
     *
     * @return PreKlient
     */
    public function setNazwa(string $nazwa): PreKlient
    {
        $this->nazwa = $nazwa;
        return $this;
    }

    /**
     * @param string $nazwaPelna
     *
     * @return PreKlient
     */
    public function setNazwaPelna(string $nazwaPelna): PreKlient
    {
        $this->nazwaPelna = $nazwaPelna;
        return $this;
    }

    /**
     * @param string $osobaImie
     *
     * @return PreKlient
     */
    public function setOsobaImie(string $osobaImie): PreKlient
    {
        $this->osobaImie = $osobaImie;
        return $this;
    }

    /**
     * @param string $osobaNazwisko
     *
     * @return PreKlient
     */
    public function setOsobaNazwisko(string $osobaNazwisko): PreKlient
    {
        $this->osobaNazwisko = $osobaNazwisko;
        return $this;
    }

    /**
     * @param string $NIP
     *
     * @return PreKlient
     */
    public function setNIP(?string $NIP): PreKlient
    {
        $this->NIP = $NIP;
        return $this;
    }

    /**
     * @param string $NIPUE
     *
     * @return PreKlient
     */
    public function setNIPUE(?string $NIPUE): PreKlient
    {
        $this->NIPUE = $NIPUE;
        return $this;
    }

    /**
     * @param string $email
     *
     * @return PreKlient
     */
    public function setEmail(string $email): PreKlient
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string $telefon
     *
     * @return PreKlient
     */
    public function setTelefon(string $telefon): PreKlient
    {
        $this->telefon = $telefon;
        return $this;
    }

    /**
     * @param string $rodzajNaDok
     *
     * @return PreKlient
     */
    public function setRodzajNaDok(string $rodzajNaDok): PreKlient
    {
        $this->rodzajNaDok = $rodzajNaDok;
        return $this;
    }

    /**
     * @param string|null $nrRachunku
     *
     * @return PreKlient
     */
    public function setNrRachunku(?string $nrRachunku): PreKlient
    {
        $this->nrRachunku = $nrRachunku;
        return $this;
    }

    /**
     * @param string $chceFV
     *
     * @return PreKlient
     */
    public function setChceFV(string $chceFV): PreKlient
    {
        $this->chceFV = $chceFV;
        return $this;
    }

    /**
     * @param PreAdres $adresGlowny
     *
     * @return PreKlient
     */
    public function setAdresGlowny(PreAdres $adresGlowny): PreKlient
    {
        $this->adresGlowny = $adresGlowny;
        return $this;
    }
}
