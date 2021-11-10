<?php


namespace App\Integrations\Pocztex;

class PotwierdzenieOdbioruBiznesowaType
{
    /**
     * Określa liczbę potwierdzeń odbioru
     *
     * @var integer
     */
    protected int $ilosc;

    /**
     * Określa sposób przekazania potwierdzenia odbioru.
     *
     * @var SposobPrzekazaniaPotwierdzeniaBiznesowaType
     */
    protected SposobPrzekazaniaPotwierdzeniaBiznesowaType $sposob;

    /**
     * PotwierdzenieOdbioruBiznesowaType constructor.
     * @param int                                         $ilosc
     * @param SposobPrzekazaniaPotwierdzeniaBiznesowaType $sposob
     */
    public function __construct(int $ilosc, SposobPrzekazaniaPotwierdzeniaBiznesowaType $sposob)
    {
        $this->ilosc = $ilosc;
        $this->sposob = $sposob;
    }

    /**
     * @return integer
     */
    public function getIlosc(): int
    {
        return $this->ilosc;
    }

    /**
     * @param integer $ilosc
     */
    public function setIlosc(int $ilosc): void
    {
        $this->ilosc = $ilosc;
    }

    /**
     * @return SposobPrzekazaniaPotwierdzeniaBiznesowaType
     */
    public function getSposob(): SposobPrzekazaniaPotwierdzeniaBiznesowaType
    {
        return $this->sposob;
    }

    /**
     * @param SposobPrzekazaniaPotwierdzeniaBiznesowaType $sposob
     */
    public function setSposob(SposobPrzekazaniaPotwierdzeniaBiznesowaType $sposob): void
    {
        $this->sposob = $sposob;
    }
}