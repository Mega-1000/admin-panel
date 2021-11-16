<?php

namespace App\Integrations\Pocztex;

/**
 * Class PrzesylkaBiznesowaType
 * @package App\Integrations\Pocztex
 *
 * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
 */
final class PrzesylkaBiznesowaType extends PrzesylkaRejestrowanaType
{
    /**
     * Atrybut opcjonalny, przeznaczony do uzupełniania dla Klientów nadających
     * przesyłki na zasadach specjalnych.
     * Należy przekazać element zgodny z interfejsem zasadySpecjalneEnum
     *
     * @var zasadySpecjalneEnum|null
     */
    protected ?zasadySpecjalneEnum $zasadySpecjalne;

    /**
     * Ciężar przesyłki w gramach.
     *
     * @var integer
     */
    protected int $masa;

    /**
     * Określa gabaryt przesyłki. Dopuszczalne wartości to: XS, S, M, L, XL, XXL.
     *
     * @var string
     */
    protected string $gabaryt;

    /**
     * TRUE jeżeli przesyłka niestandardowa. Za przesyłkę niestandardową uważa się przesyłkę
     * spełniającą przynajmniej jedno z poniższych kryteriów:
     * wymiary wynoszące 250 cm < (a+b+c) < 300 cm, przy czym dł. maks. = 150 cm,
     * wymiary wynoszące (a+b+c) <= 250 cm, przy czym dł. maks. = 150 cm, posiadającej:
     * - nieregularne kształty lub
     * - wystające elementy, lub
     * - składającej się z dwóch odrębnych części, połączonych w jedną nieregularną całość (np. za pomocą folii stretch,taśmy itp.).
     *
     * @var boolean
     */
    protected bool $niestandardowa;

    /**
     * Określenie wartości nadawanej przesyłki. Określenie wartości jest równoznacznie z chęcią skorzystania z usługi
     * przesyłka z określoną wartością (w tym wypadku pole to jest wymagane). Kwotę należy podać w groszach.
     *
     * @var integer
     */
    protected int $wartosc;

    /**
     * Wartość logiczna określająca korzystanie z usługi ostrożnie
     *
     * @var bool
     */
    protected bool $ostroznie;

    /**
     * Numer transakcji
     *
     * @var integer|null
     */
    protected ?int $numerTransakcjiOdbioru;

    /**
     * Element typu pobranieType. Opisujący pobranie. Jedynym możliwym sposobem pobrania dla tego typu
     * przesyłki to wpłata na rachunek bankowy.
     *
     * @var pobranieType|null
     */
    protected ?pobranieType $pobranie;

    /**
     * Określenie, w jakim urzędzie ma zostać odebrana przesyłka.
     * Lista urzędów możliwa do pobrania metodą getPlacowkiPocztowe.
     *
     * @var placowkaPocztowaType|null
     */
    protected ?placowkaPocztowaType $urzadWydaniaEPrzesylki;

    /**
     * Elementy typu subPrzesylkaBiznesowaType (minimalna ilość wystąpień 4).
     *
     * @var subPrzesylkaBiznesowaType[]|null
     */
    protected ?array $subPrzesylka;

    /**
     * Element typu ubezpieczenieType określający rodzaj ubezpieczenia przesyłki.
     *
     * @var ubezpieczenieType|null
     */
    protected ?ubezpieczenieType $ubezpieczenie;

    /**
     * Określenie usługi komplementarnej EPO. Należy przekazać element zgodny z interfejsem EPOType,
     * obecnie możliwe są dwa typy EPOSimpleType lub EPOExtendedType. Atrybut występuje tylko w przypadku
     * podpisanej umowy na EPO do paczki pocztowej
     *
     * @var EPOType|null
     */
    protected ?EPOType $epo;

    /**
     * Element zawierający adres na który zostanie zwrócona przesyłka w przypadku nieodebrania przez adresata (zwrot przesyłki).
     *
     * @var adresType|null
     */
    protected ?adresType $adresDlaZwrotu;

    /**
     * Określa usługę komplementarną Sprawdzenie zawartości przez odbiorcę
     *
     * @var boolean
     */
    protected bool $sprawdzenieZawartosciPrzesylkiPrzezOdbiorce;

    /**
     * Określa usługę komplementarną Potwierdzenie odbioru.
     *
     * @var PotwierdzenieOdbioruBiznesowaType|null
     */
    protected ?PotwierdzenieOdbioruBiznesowaType $potwierdzenieOdbioru;

    /**
     * Określa usługę komplementarne dotyczące doręczenia przesyłki
     *
     * @var PotwierdzenieOdbioruBiznesowaType|null
     */
    protected ?PotwierdzenieOdbioruBiznesowaType $doreczenie;

    /**
     * Określa usługę komplementarną Dokumenty zwrotne
     *
     * @var ZwrotDokumentowBiznesowaType|null
     */
    protected ?ZwrotDokumentowBiznesowaType $zwrotDokumentow;

    /**
     * PrzesylkaBiznesowaType constructor.
     *
     * @param integer                                $masa
     * @param string                                 $gabaryt
     * @param boolean                                $niestandardowa
     * @param integer                                $wartosc
     * @param boolean                                $ostroznie
     * @param adresType                              $adresType
     * @param zasadySpecjalneEnum|null               $zasadySpecjalne
     * @param integer|null                           $numerTransakcjiOdbioru
     * @param pobranieType|null                      $pobranie
     * @param placowkaPocztowaType|null              $urzadWydaniaEPrzesylki
     * @param subPrzesylkaBiznesowaType[]            $subPrzesylka
     * @param ubezpieczenieType|null                 $ubezpieczenie
     * @param EPOType|null                           $epo
     * @param adresType|null                         $adresDlaZwrotu
     * @param boolean                                $sprawdzenieZawartosciPrzesylkiPrzezOdbiorce
     * @param PotwierdzenieOdbioruBiznesowaType|null $potwierdzenieOdbioru
     * @param PotwierdzenieOdbioruBiznesowaType|null $doreczenie
     * @param ZwrotDokumentowBiznesowaType|null      $zwrotDokumentow
     * @param string|null                            $guid
     */
    public function __construct(
        int $masa,
        string $gabaryt,
        bool $niestandardowa,
        int $wartosc,
        bool $ostroznie,
        adresType $adresType,
        ?zasadySpecjalneEnum $zasadySpecjalne,
        ?int $numerTransakcjiOdbioru = null,
        ?pobranieType $pobranie = null,
        ?placowkaPocztowaType $urzadWydaniaEPrzesylki = null,
        ?array $subPrzesylka = null,
        ?ubezpieczenieType $ubezpieczenie = null,
        ?EPOType $epo = null,
        ?adresType $adresDlaZwrotu = null,
        bool $sprawdzenieZawartosciPrzesylkiPrzezOdbiorce = false,
        ?PotwierdzenieOdbioruBiznesowaType $potwierdzenieOdbioru = null,
        ?PotwierdzenieOdbioruBiznesowaType $doreczenie = null,
        ?ZwrotDokumentowBiznesowaType $zwrotDokumentow = null,
        ?string $guid = null
    )
    {
        $this->zasadySpecjalne = $zasadySpecjalne;
        $this->masa = $masa;
        $this->gabaryt = $gabaryt;
        $this->niestandardowa = $niestandardowa;
        $this->wartosc = $wartosc;
        $this->ostroznie = $ostroznie;
        $this->numerTransakcjiOdbioru = $numerTransakcjiOdbioru;
        $this->pobranie = $pobranie;
        $this->urzadWydaniaEPrzesylki = $urzadWydaniaEPrzesylki;
        $this->subPrzesylka = $subPrzesylka;
        $this->ubezpieczenie = $ubezpieczenie;
        $this->epo = $epo;
        $this->adresDlaZwrotu = $adresDlaZwrotu;
        $this->sprawdzenieZawartosciPrzesylkiPrzezOdbiorce = $sprawdzenieZawartosciPrzesylkiPrzezOdbiorce;
        $this->potwierdzenieOdbioru = $potwierdzenieOdbioru;
        $this->doreczenie = $doreczenie;
        $this->zwrotDokumentow = $zwrotDokumentow;
        $this->guid = $guid;
    }

    /**
     * @return zasadySpecjalneEnum|null
     */
    public function getZasadySpecjalne(): ?zasadySpecjalneEnum
    {
        return $this->zasadySpecjalne;
    }

    /**
     * @param zasadySpecjalneEnum|null $zasadySpecjalne
     */
    public function setZasadySpecjalne(?zasadySpecjalneEnum $zasadySpecjalne): void
    {
        $this->zasadySpecjalne = $zasadySpecjalne;
    }

    /**
     * @return int
     */
    public function getMasa(): int
    {
        return $this->masa;
    }

    /**
     * @param int $masa
     */
    public function setMasa(int $masa): void
    {
        $this->masa = $masa;
    }

    /**
     * @return string
     */
    public function getGabaryt(): string
    {
        return $this->gabaryt;
    }

    /**
     * @param string $gabaryt
     */
    public function setGabaryt(string $gabaryt): void
    {
        $this->gabaryt = $gabaryt;
    }

    /**
     * @return bool
     */
    public function isNiestandardowa(): bool
    {
        return $this->niestandardowa;
    }

    /**
     * @param bool $niestandardowa
     */
    public function setNiestandardowa(bool $niestandardowa): void
    {
        $this->niestandardowa = $niestandardowa;
    }

    /**
     * @return int
     */
    public function getWartosc(): int
    {
        return $this->wartosc;
    }

    /**
     * @param int $wartosc
     */
    public function setWartosc(int $wartosc): void
    {
        $this->wartosc = $wartosc;
    }

    /**
     * @return bool
     */
    public function isOstroznie(): bool
    {
        return $this->ostroznie;
    }

    /**
     * @param bool $ostroznie
     */
    public function setOstroznie(bool $ostroznie): void
    {
        $this->ostroznie = $ostroznie;
    }

    /**
     * @return int|null
     */
    public function getNumerTransakcjiOdbioru(): ?int
    {
        return $this->numerTransakcjiOdbioru;
    }

    /**
     * @param int|null $numerTransakcjiOdbioru
     */
    public function setNumerTransakcjiOdbioru(?int $numerTransakcjiOdbioru): void
    {
        $this->numerTransakcjiOdbioru = $numerTransakcjiOdbioru;
    }

    /**
     * @return pobranieType|null
     */
    public function getPobranie(): ?pobranieType
    {
        return $this->pobranie;
    }

    /**
     * @param pobranieType|null $pobranie
     */
    public function setPobranie(?pobranieType $pobranie): void
    {
        $this->pobranie = $pobranie;
    }

    /**
     * @return placowkaPocztowaType|null
     */
    public function getUrzadWydaniaEPrzesylki(): ?placowkaPocztowaType
    {
        return $this->urzadWydaniaEPrzesylki;
    }

    /**
     * @param placowkaPocztowaType|null $urzadWydaniaEPrzesylki
     */
    public function setUrzadWydaniaEPrzesylki(?placowkaPocztowaType $urzadWydaniaEPrzesylki): void
    {
        $this->urzadWydaniaEPrzesylki = $urzadWydaniaEPrzesylki;
    }

    /**
     * @return subPrzesylkaBiznesowaType[]|null
     */
    public function getSubPrzesylka(): ?array
    {
        return $this->subPrzesylka;
    }

    /**
     * @param subPrzesylkaBiznesowaType[]|null $subPrzesylka
     */
    public function setSubPrzesylka(?array $subPrzesylka): void
    {
        $this->subPrzesylka = $subPrzesylka;
    }

    /**
     * @return ubezpieczenieType|null
     */
    public function getUbezpieczenie(): ?ubezpieczenieType
    {
        return $this->ubezpieczenie;
    }

    /**
     * @param ubezpieczenieType|null $ubezpieczenie
     */
    public function setUbezpieczenie(?ubezpieczenieType $ubezpieczenie): void
    {
        $this->ubezpieczenie = $ubezpieczenie;
    }

    /**
     * @return EPOType|null
     */
    public function getEpo(): ?EPOType
    {
        return $this->epo;
    }

    /**
     * @param EPOType|null $epo
     */
    public function setEpo(?EPOType $epo): void
    {
        $this->epo = $epo;
    }

    /**
     * @return adresType|null
     */
    public function getAdresDlaZwrotu(): ?adresType
    {
        return $this->adresDlaZwrotu;
    }

    /**
     * @param adresType|null $adresDlaZwrotu
     */
    public function setAdresDlaZwrotu(?adresType $adresDlaZwrotu): void
    {
        $this->adresDlaZwrotu = $adresDlaZwrotu;
    }

    /**
     * @return bool
     */
    public function isSprawdzenieZawartosciPrzesylkiPrzezOdbiorce(): bool
    {
        return $this->sprawdzenieZawartosciPrzesylkiPrzezOdbiorce;
    }

    /**
     * @param bool $sprawdzenieZawartosciPrzesylkiPrzezOdbiorce
     */
    public function setSprawdzenieZawartosciPrzesylkiPrzezOdbiorce(bool $sprawdzenieZawartosciPrzesylkiPrzezOdbiorce): void
    {
        $this->sprawdzenieZawartosciPrzesylkiPrzezOdbiorce = $sprawdzenieZawartosciPrzesylkiPrzezOdbiorce;
    }

    /**
     * @return PotwierdzenieOdbioruBiznesowaType|null
     */
    public function getPotwierdzenieOdbioru(): ?PotwierdzenieOdbioruBiznesowaType
    {
        return $this->potwierdzenieOdbioru;
    }

    /**
     * @param PotwierdzenieOdbioruBiznesowaType|null $potwierdzenieOdbioru
     */
    public function setPotwierdzenieOdbioru(?PotwierdzenieOdbioruBiznesowaType $potwierdzenieOdbioru): void
    {
        $this->potwierdzenieOdbioru = $potwierdzenieOdbioru;
    }

    /**
     * @return PotwierdzenieOdbioruBiznesowaType|null
     */
    public function getDoreczenie(): ?PotwierdzenieOdbioruBiznesowaType
    {
        return $this->doreczenie;
    }

    /**
     * @param PotwierdzenieOdbioruBiznesowaType|null $doreczenie
     */
    public function setDoreczenie(?PotwierdzenieOdbioruBiznesowaType $doreczenie): void
    {
        $this->doreczenie = $doreczenie;
    }

    /**
     * @return ZwrotDokumentowBiznesowaType|null
     */
    public function getZwrotDokumentow(): ?ZwrotDokumentowBiznesowaType
    {
        return $this->zwrotDokumentow;
    }

    /**
     * @param ZwrotDokumentowBiznesowaType|null $zwrotDokumentow
     */
    public function setZwrotDokumentow(?ZwrotDokumentowBiznesowaType $zwrotDokumentow): void
    {
        $this->zwrotDokumentow = $zwrotDokumentow;
    }
}