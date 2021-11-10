<?php

namespace App\Integrations\Pocztex;

/**
 * Class PrzesylkaBiznesowaType
 * @package App\Integrations\Pocztex
 *
 * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
 */
final class PrzesylkaBiznesowaType extends przesylkaRejestrowanaType
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
     * @var gabarytBiznesowaType
     */
    protected gabarytBiznesowaType $gabaryt;

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
     * @var int
     */
    protected int $numerTransakcjiOdbioru;

    /**
     * Element typu pobranieType. Opisujący pobranie. Jedynym możliwym sposobem pobrania dla tego typu
     * przesyłki to wpłata na rachunek bankowy.
     *
     * @var pobranieType
     */
    protected pobranieType $pobranie;

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
     * @var subPrzesylkaBiznesowaType
     */
    protected subPrzesylkaBiznesowaType $subPrzesylka;

    /**
     * Element typu ubezpieczenieType określający rodzaj ubezpieczenia przesyłki.
     *
     * @var ubezpieczenieType
     */
    protected ubezpieczenieType $ubezpieczenie;

    /**
     * Określenie usługi komplementarnej EPO. Należy przekazać element zgodny z interfejsem EPOType,
     * obecnie możliwe są dwa typy EPOSimpleType lub EPOExtendedType. Atrybut występuje tylko w przypadku
     * podpisanej umowy na EPO do paczki pocztowej
     *
     * @var EPOType
     */
    protected EPOType $epo;

    /**
     * Element zawierający adres na który zostanie zwrócona przesyłka w przypadku nieodebrania przez adresata (zwrot przesyłki).
     *
     * @var adresType
     */
    protected adresType $adresDlaZwrotu;

    /**
     * Określa usługę komplementarną Sprawdzenie zawartości przez odbiorcę
     *
     * @var boolean
     */
    protected bool $sprawdzenieZawartosciPrzesylkiPrzezOdbiorce;

    /**
     * Określa usługę komplementarną Potwierdzenie odbioru.
     *
     * @var PotwierdzenieOdbioruBiznesowaType
     */
    protected PotwierdzenieOdbioruBiznesowaType $potwierdzenieOdbioru;

    /**
     * Określa usługę komplementarne dotyczące doręczenia przesyłki
     *
     * @var PotwierdzenieOdbioruBiznesowaType
     */
    protected PotwierdzenieOdbioruBiznesowaType $doreczenie;

    /**
     * Określa usługę komplementarną Dokumenty zwrotne
     *
     * @var ZwrotDokumentowBiznesowaType
     */
    protected ZwrotDokumentowBiznesowaType $zwrotDokumentow;



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
     * @return gabarytBiznesowaType
     */
    public function getGabaryt(): gabarytBiznesowaType
    {
        return $this->gabaryt;
    }

    /**
     * @param gabarytBiznesowaType $gabaryt
     */
    public function setGabaryt(gabarytBiznesowaType $gabaryt): void
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
     * @return mixed
     */
    public function getNumerTransakcjiOdbioru()
    {
        return $this->numerTransakcjiOdbioru;
    }

    /**
     * @param mixed $numerTransakcjiOdbioru
     */
    public function setNumerTransakcjiOdbioru($numerTransakcjiOdbioru): void
    {
        $this->numerTransakcjiOdbioru = $numerTransakcjiOdbioru;
    }

    /**
     * @return pobranieType
     */
    public function getPobranie(): pobranieType
    {
        return $this->pobranie;
    }

    /**
     * @param pobranieType $pobranie
     */
    public function setPobranie(pobranieType $pobranie): void
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
     * @return subPrzesylkaBiznesowaType
     */
    public function getSubPrzesylka(): subPrzesylkaBiznesowaType
    {
        return $this->subPrzesylka;
    }

    /**
     * @param subPrzesylkaBiznesowaType $subPrzesylka
     */
    public function setSubPrzesylka(subPrzesylkaBiznesowaType $subPrzesylka): void
    {
        $this->subPrzesylka = $subPrzesylka;
    }

    /**
     * @return ubezpieczenieType
     */
    public function getUbezpieczenie(): ubezpieczenieType
    {
        return $this->ubezpieczenie;
    }

    /**
     * @param ubezpieczenieType $ubezpieczenie
     */
    public function setUbezpieczenie(ubezpieczenieType $ubezpieczenie): void
    {
        $this->ubezpieczenie = $ubezpieczenie;
    }

    /**
     * @return EPOType
     */
    public function getEpo(): EPOType
    {
        return $this->epo;
    }

    /**
     * @param EPOType $epo
     */
    public function setEpo(EPOType $epo): void
    {
        $this->epo = $epo;
    }

    /**
     * @return adresType
     */
    public function getAdresDlaZwrotu(): adresType
    {
        return $this->adresDlaZwrotu;
    }

    /**
     * @param adresType $adresDlaZwrotu
     */
    public function setAdresDlaZwrotu(adresType $adresDlaZwrotu): void
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
     * @return PotwierdzenieOdbioruBiznesowaType
     */
    public function getPotwierdzenieOdbioru(): PotwierdzenieOdbioruBiznesowaType
    {
        return $this->potwierdzenieOdbioru;
    }

    /**
     * @param PotwierdzenieOdbioruBiznesowaType $potwierdzenieOdbioru
     */
    public function setPotwierdzenieOdbioru(PotwierdzenieOdbioruBiznesowaType $potwierdzenieOdbioru): void
    {
        $this->potwierdzenieOdbioru = $potwierdzenieOdbioru;
    }

    /**
     * @return PotwierdzenieOdbioruBiznesowaType
     */
    public function getDoreczenie(): PotwierdzenieOdbioruBiznesowaType
    {
        return $this->doreczenie;
    }

    /**
     * @param PotwierdzenieOdbioruBiznesowaType $doreczenie
     */
    public function setDoreczenie(PotwierdzenieOdbioruBiznesowaType $doreczenie): void
    {
        $this->doreczenie = $doreczenie;
    }

    /**
     * @return ZwrotDokumentowBiznesowaType
     */
    public function getZwrotDokumentow(): ZwrotDokumentowBiznesowaType
    {
        return $this->zwrotDokumentow;
    }

    /**
     * @param ZwrotDokumentowBiznesowaType $zwrotDokumentow
     */
    public function setZwrotDokumentow(ZwrotDokumentowBiznesowaType $zwrotDokumentow): void
    {
        $this->zwrotDokumentow = $zwrotDokumentow;
    }
}