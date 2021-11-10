<?php

namespace App\Integrations\Pocztex;

/**
 * Class ZwrotDokumentowType
 * @package App\Integrations\Pocztex
 *
 * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
 */
class ZwrotDokumentowBiznesowaType
{
    /**
     * Element określający zwrot dokumentów
     *
     * @var TerminZwrotDokumentowBiznesowaType
     */
    protected TerminZwrotDokumentowBiznesowaType $rodzaj;

    /**
     * ID profilu adresowego dla dokumentów zwrotnych.
     *
     * @var integer
     */
    protected int $idDokumentyZwrotneAdresy;

    /**
     * ZwrotDokumentowBiznesowaType constructor.
     * @param TerminZwrotDokumentowBiznesowaType $rodzaj
     * @param int                                $idDokumentyZwrotneAdresy
     */
    public function __construct(TerminZwrotDokumentowBiznesowaType $rodzaj, int $idDokumentyZwrotneAdresy)
    {
        $this->rodzaj = $rodzaj;
        $this->idDokumentyZwrotneAdresy = $idDokumentyZwrotneAdresy;
    }

    /**
     * @return TerminZwrotDokumentowBiznesowaType
     */
    public function getRodzaj(): TerminZwrotDokumentowBiznesowaType
    {
        return $this->rodzaj;
    }

    /**
     * @param TerminZwrotDokumentowBiznesowaType $rodzaj
     */
    public function setRodzaj(TerminZwrotDokumentowBiznesowaType $rodzaj): void
    {
        $this->rodzaj = $rodzaj;
    }

    /**
     * @return int
     */
    public function getIdDokumentyZwrotneAdresy(): int
    {
        return $this->idDokumentyZwrotneAdresy;
    }

    /**
     * @param int $idDokumentyZwrotneAdresy
     */
    public function setIdDokumentyZwrotneAdresy(int $idDokumentyZwrotneAdresy): void
    {
        $this->idDokumentyZwrotneAdresy = $idDokumentyZwrotneAdresy;
    }
}