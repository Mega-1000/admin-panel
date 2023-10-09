<?php

namespace App\DTO\ProductPositioning;

use App\Entities\Product;
use App\Traits\ArrayOperations;
use phpDocumentor\Reflection\Types\Boolean;

/**
 * IJHWOG - ilosc jednostek handlowych w opakowaniu globalnym
 * IJHWOZ - ilosc jednostek handlowych w opakowniu zbiorczym
 * IOHWOP1 - ilosc opakowan handlowych w opakowaniu P1
 * IJHNKWWOZ - ilosc jednostek handlowych na komletnej warstwie w opakowaniu zbiorczym
 * IJZNKWWOG - ilosc jednostek zbiorczych na kompletnej warstwie w opakowani globalnym
 * IWJNWPWOZ - ilosc  warstw jednostek handlowycyh w pionowe w opakowaniu zbiorczym
 * IPHWOZPD - ilosc opakowan handlowych w opakowaniu zbioczym po dlugosci
 * IPHWOZPS - ilosc opakowan handlowych w opakowaniu zbioczym po szerokosci
 * IJZPDWOG - ilosc jednostek zbiorczych po dlugosci w opakowaniu globalnym
 * IJZPSWOG - ilosc jednostek zbiorczych po szerokosci w opakowaniu globalnym
 *
 * IOHKSPWZIP1NPWW1WOH - ilosc opakowan handlowych ktore sa pakowane w zbiorczych ilosciach P1 na pelnej warstwie W1 w opakowaniu handlowym
 *
 * IKWJZWOG - ilosc kopletnych warstw jednostek zbiorczych w opakowaniu globalnym
 * IPJZNRWWOG - ilosc pelnych jednostek zbiorczych na rozpoczetej warstwie w opakowaniu globalnym
 * IJHWROZNRWZWJG - ilosc jednostek handlowych w rozpoczetym opakowaniu zbiorczym na rozpoczetej warstwie zbiorczej w jednostce globalnej
 *
 * IKROZPDWRWOG - ilosc kompletnych rzedow opakowan zbiorczych po dlugosci w rozpoczetej warstwie w opakowaniu globalnym
 * IKOZWRRNRWWOG - ilosc kompletnych opakowan zbiorczych w rozpoczetym rzedzie na rozpoczeteje warstwie w opakowaniu globalnym
 *
 *
 * IPWJHWROZWOG - ilosc pelnych warstw jednostek handlowych w rozpoczetym opakowaniu zbirczym w opakowaniu globalnym
 * IKRPDOHNRWWRIZBRWWOG - ilosc kompletnych rzedow po dlugosci opakowan handlowych na rozpoczetej warstwie w rozpoczetym opakowaniu zbiorczym na rozpoczetej warstwie w opakowaniu globalnym
 * IOHWRRWROZWRWWOG - ilosc opakowan handlowych w rozpoczetym rzedzie w rozpoczetym opakowaniu zbiorczym w rozpoczetej warstwie w opakowaniu globalnym
 *
 *
 * IKWW1WROZWRWWROG - ilosc kompletnych warst W1 w rozpocztetym opakowaniu zbiorczym w rozpoczetej warstwie w rozpoczeteym opakowaniu globalnym
 * IKOP1WRWWW1WOG - ilosc kompletnych opakowan P1 w rozpoczetej warstwie warstwie W1 w opakowaniu globlanym
 * IOHWROP1WRWWOG - ilosc opakowan handlowych w niepelnym opkawaniu P1 w rozpcztej warstwie w opakowaniu zbiorczym w opakowaniu globalnym
 */
readonly final class ProductPositioningDTO
{
    use ArrayOperations;

    public function __construct(
        public float $IJHWOZ,
        public float $IJHWOG,
        public float $IOHWOP1,
        public float $IJHNKWWOZ,
        public float $IJZNKWWOG,
        public float $IWJNWPWOZ,
        public float $IPHWOZPD,
        public float $IPHWOZPS,
        public float $IJZPDWOG,
        public float $IJZPSWOG,
        public float $IOHKSPWZIP1NPWW1WOH,
        public float $IKWJZWOG,
        public float $IPJZNRWWOG,
        public float $IJHWROZNRWZWJG,
        public float $IKROZPDWRWOG,
        public float $IKOZWRRNRWWOG,
        public float $IPWJHWROZWOG,
        public float $IKRPDOHNRWWRIZBRWWOG,
        public float $IOHWRRWROZWRWWOG,
        public float $IKWW1WROZWRWWROG,
        public float $IKOP1WRWWW1WOG,
        public float $IOHWROP1WRWWOG,
        public bool $isZero = false,
        public Product $product,
    ) {}

    public static function fromAcronymsArray(array $data): self
    {
        return new self(
            IJHWOZ: self::getFloatValue($data, 'IJHWOZ'),
            IJHWOG: self::getFloatValue($data, 'IJHWOG'),
            IOHWOP1: self::getFloatValue($data, 'IOHWOP1'),
            IJHNKWWOZ: self::getFloatValue($data, 'IJHNKWWOZ'),
            IJZNKWWOG: self::getFloatValue($data, 'IJZNKWWOG'),
            IWJNWPWOZ: self::getFloatValue($data, 'IWJNWPWOZ'),
            IPHWOZPD: self::getFloatValue($data, 'IPHWOZPD'),
            IPHWOZPS: self::getFloatValue($data, 'IPHWOZPS'),
            IJZPDWOG: self::getFloatValue($data, 'IJZPDWOG'),
            IJZPSWOG: self::getFloatValue($data, 'IJZPSWOG'),
            IOHKSPWZIP1NPWW1WOH: self::getFloatValue($data, 'IOHKSPWZIP1NPWW1WOH'),
            IKWJZWOG: self::getFloatValue($data, 'IKWJZWOG'),
            IPJZNRWWOG: self::getFloatValue($data, 'IPJZNRWWOG'),
            IJHWROZNRWZWJG: self::getFloatValue($data, 'IJHWROZNRWZWJG'),
            IKROZPDWRWOG: self::getFloatValue($data, 'IKROZPDWRWOG'),
            IKOZWRRNRWWOG: self::getFloatValue($data, 'IKOZWRRNRWWOG'),
            IPWJHWROZWOG: self::getFloatValue($data, 'IPWJHWROZWOG'),
            IKRPDOHNRWWRIZBRWWOG: self::getFloatValue($data, 'IKRPDOHNRWWRIZBRWWOG'),
            IOHWRRWROZWRWWOG: self::getFloatValue($data, 'IOHWRRWROZWRWWOG'),
            IKWW1WROZWRWWROG: self::getFloatValue($data, 'IKWW1WROZWRWWROG'),
            IKOP1WRWWW1WOG: self::getFloatValue($data, 'IKOP1WRWWW1WOG'),
            IOHWROP1WRWWOG: self::getFloatValue($data, 'IOHWROP1WRWWOG'),
            isZero: $data['isZero'],
            product: $data['product'],
        );
    }

    public function isZero(): bool
    {
        return $this->isZero;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
