<?php

namespace App\Integrations\Artoit;

use App\Integrations\Pocztex\statusAccountType;

/**
 * PreAdres class
 */
class PreAdres
{
    /**
     * @var string
     */
    public $nazwa;

    /**
     * @var string
     */
    public $ulica;

    /**
     * @var string
     */
    public $miasto;

    /**
     * @var string
     */
    public $kod;

    /**
     * @var string
     */
    public $panstwo;

    /**
     * @param string $nazwa
     */
    public function setNazwa($nazwa): self
    {
        $this->nazwa = $nazwa;

        return $this;
    }

    /**
     * @param string $ulica
     */
    public function setUlica($ulica): self
    {
        $this->ulica = $ulica;

        return $this;
    }

    /**
     * @param string $miasto
     */
    public function setMiasto($miasto): self
    {
        $this->miasto = $miasto;

        return $this;
    }

    /**
     * @param string $kod
     */
    public function setKod($kod): self
    {
        $this->kod = $kod;

        return $this;
    }

    /**
     * @param string $panstwo
     */
    public function setPanstwo($panstwo): self
    {
        $this->panstwo = $panstwo;

        return $this;
    }
}
