<?php

namespace App\Integrations\Artoit;

class PreKlient
{

    public $typ;

    public $symbol;
    public $nazwa;
    public $nazwaPelna;
    public $osobaImie;
    public $osobaNazwisko;
    public $NIP;
    public $NIPUE;
    public $email;
    public $telefon;
    public $rodzajNaDok;
    public $nrRachunku;
    public $chceFV;
    public $adresGlowny;

    /**
     * @param EPreKlientTyp|string|null $typ
     *
     * return self
     */
    public function setTyp($typ): self
    {
        $this->typ = $typ;
        return $this;
    }

    /**
     * @param string|null $symbol
     *
     * return self
     */
    public function setSymbol(?string $symbol): self
    {
        $rawSymbol = explode('-', $symbol);
        $this->symbol = $rawSymbol[0];
        return $this;
    }

    /**
     * @param string|null $nazwa
     *
     * return self
     */
    public function setNazwa(?string $nazwa): self
    {
        $this->nazwa = $nazwa;
        return $this;
    }

    /**
     * @param string|null $nazwaPelna
     *
     * return self
     */
    public function setNazwaPelna(?string $nazwaPelna): self
    {
        $this->nazwaPelna = $nazwaPelna;
        return $this;
    }

    /**
     * @param string|null $osobaImie
     *
     * return self
     */
    public function setOsobaImie(?string $osobaImie): self
    {
        $this->osobaImie = $osobaImie;
        return $this;
    }

    /**
     * @param string|null $osobaNazwisko
     *
     * return self
     */
    public function setOsobaNazwisko(?string $osobaNazwisko): self
    {
        $this->osobaNazwisko = $osobaNazwisko;
        return $this;
    }

    /**
     * @param string|null $NIP
     *
     * return self
     */
    public function setNIP(?string $NIP): self
    {
        $this->NIP = $NIP;
        return $this;
    }

    /**
     * @param string|null $NIPUE
     *
     * return self
     */
    public function setNIPUE(?string $NIPUE): self
    {
        $this->NIPUE = $NIPUE;
        return $this;
    }

    /**
     * @param string|null $email
     *
     * return self
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @param string|null $telefon
     *
     * return self
     */
    public function setTelefon(?string $telefon): self
    {
        $this->telefon = $telefon;
        return $this;
    }

    /**
     * @param EPreKlientRodzajNaDok|string|null $rodzajNaDok
     *
     * return self
     */
    public function setRodzajNaDok($rodzajNaDok): self
    {
        $this->rodzajNaDok = $rodzajNaDok;
        return $this;
    }

    /**
     * @param string|null $nrRachunku
     *
     * return self
     */
    public function setNrRachunku(?string $nrRachunku): self
    {
        $this->nrRachunku = $nrRachunku;
        return $this;
    }

    /**
     * @param string|null $chceFV
     *
     * return self
     */
    public function setChceFV(?string $chceFV): self
    {
        $this->chceFV = $chceFV;
        return $this;
    }

    /**
     * @param PreAdres|null $adresGlowny
     *
     * return self
     */
    public function setAdresGlowny(?PreAdres $adresGlowny): self
    {
        $this->adresGlowny = $adresGlowny;
        return $this;
    }
}
