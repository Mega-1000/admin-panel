<?php

namespace App\Integrations\Artoit;

class PreAdres
{
    private $nazwa;
    private $ulica;
    private $miasto;
    private $kod;
    private $panstwo;

    /**
     * @param $nazwa
     * @param $ulica
     * @param $miasto
     * @param $kod
     * @param $panstwo
     */
    public function __construct(?string $nazwa, ?string $ulica, ?string $miasto, ?string $kod, ?string $panstwo)
    {
        $this->nazwa = $nazwa;
        $this->ulica = $ulica;
        $this->miasto = $miasto;
        $this->kod = $kod;
        $this->panstwo = $panstwo;
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
    public function getUlica(): ?string
    {
        return $this->ulica;
    }

    /**
     * @return string|null
     */
    public function getMiasto(): ?string
    {
        return $this->miasto;
    }

    /**
     * @return string|null
     */
    public function getKod(): ?string
    {
        return $this->kod;
    }

    /**
     * @return string|null
     */
    public function getPanstwo(): ?string
    {
        return $this->panstwo;
    }
}
