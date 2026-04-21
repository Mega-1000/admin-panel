<?php

namespace App\Enums\Schenker;

use App\Enums\PackageStatus;

class TrackingCode
{

    /** @var string Zlecono odbiór przesyłki */
    const EVENT_CODE_BOOKED = 'ENT';
    /** @var string Przesyłka odebrana przez Schenker */
    const EVENT_CODE_COLLECTED = 'COL';
    /** @var string Nadawca dostarczył przesyłkę do terminala Schenker */
    const EVENT_CODE_DELIVERED_TO_TERMINAL_BY_SHIPPER = 'DET';
    /** @var string Przesyłka opuściła terminal nadania. Status MAN nie jest tworzony
     * dla przesyłek nie przejeżdżających przez terminal oraz dla przesyłek
     * typu PARCEL i PREMIUM_PARCEL.
     */
    const EVENT_CODE_DEPARTED = 'MAN';
    /** @var string Przesyłka została dostarczona do terminalu docelowego */
    const EVENT_CODE_ARRIVED_TO_DESTINATION_TERMINAL = 'ENM';
    /** @var string Obsługa celna została rozpoczęta */
    const EVENT_CODE_CUSTOMS_CLEARANCE_INITIATED = 'CCL';
    /** @var string Obsługa celna została zakończona */
    const EVENT_CODE_CUSTOMS_CLEARANCE_FINALISED = 'CCF';
    /** @var string Przesyłka oczekuje na decyzję odbiorcy. */
    const EVENT_CODE_TO_CONSIGNEES_DISPOSAL = 'DIS';
    /** @var string Przesyłka została odebrana przez Odbiorcę bezpośrednio z terminalu Schenker */
    const EVENT_CODE_PICKED_UP_BY_CONSIGNEE = 'PUP';
    /** @var string Przesyłka przekazana kierowcy do celem dostawy. */
    const EVENT_CODE_OUT_OF_DELIVERY = 'DOT';
    /** @var string Przesyłka została dostarczona */
    const EVENT_CODE_DELIVERED = 'DLV';
    /** @var string Przesyłka zeskanowana na terminalu */
    const EVENT_CODE_TERMINAL_INVENTORY = 'TIN';
    /** @var string Przesyłka nie została dostarczona. Wraz z tym statusem będzie podany
     * powód niedostarczenia wg słownika poniżej.
     */
    const EVENT_CODE_NOT_DELIVERED = 'NDL';
    /** @var string Elektroniczne potwierdzenie dostawy (z podpisem odbiorcy) jest dostępne */
    const EVENT_CODE_EPOD_AVAILABLE = 'POD';

    const DB_MAPPING_EVENT_CODE_TO_PACKAGE_STATUS = [
        PackageStatus::DELIVERED => [
            self::EVENT_CODE_DELIVERED,
            self::EVENT_CODE_PICKED_UP_BY_CONSIGNEE,
        ],
        PackageStatus::CANCELLED => [
            self::EVENT_CODE_NOT_DELIVERED,
        ],
        PackageStatus::WAITING_FOR_SENDING => [
            self::EVENT_CODE_BOOKED,
            self::EVENT_CODE_EPOD_AVAILABLE,
        ],
        PackageStatus::SENDING => [
            self::EVENT_CODE_COLLECTED,
            self::EVENT_CODE_DELIVERED_TO_TERMINAL_BY_SHIPPER,
            self::EVENT_CODE_DEPARTED,
            self::EVENT_CODE_ARRIVED_TO_DESTINATION_TERMINAL,
            self::EVENT_CODE_CUSTOMS_CLEARANCE_INITIATED,
            self::EVENT_CODE_CUSTOMS_CLEARANCE_FINALISED,
            self::EVENT_CODE_TO_CONSIGNEES_DISPOSAL,
            self::EVENT_CODE_OUT_OF_DELIVERY,
            self::EVENT_CODE_TERMINAL_INVENTORY,
        ]
    ];

    const EVENT_REASON_ORDER_CANCELED = 'BC';
    const EVENT_REASON_DELIVERY_DATE_CHANGED_BY_RECIPIENT = 'CD';
    const EVENT_REASON_DELIVERY_DATE_CHANGED_BY_SENDER = 'SD';
    const EVENT_REASON_CLOSED = 'CL';
    const EVENT_REASON_COD_NOT_PAID = 'NP';
    const EVENT_REASON_NO_DELIVERED_RECIPIENT_ERROR = 'CC';
    const EVENT_REASON_RECIPIENT_NOTIFIED_ABOUT_PACKAGE = 'PA';
    const EVENT_REASON_ACT_OF_GOD = 'FM';
    const EVENT_REASON_INCOMPLETE_SHIPMENT = 'IN';
    const EVENT_REASON_INCORRECT_SHIPMENT_DATA = 'IA';
    const EVENT_REASON_DELAY_BY_SENDER = 'LC';
    const EVENT_REASON_DELAY_BY_CUSTOMS_PROCEDURE = 'CU';
    const EVENT_REASON_SHIPMENT_LOST = 'LS';
    const EVENT_REASON_SHIPMENT_DAMAGED = 'MA';
    const EVENT_REASON_DELIVERY_PLEDGED = 'PF';
    const EVENT_REASON_REJECTED_BY_RECIPIENT = 'RC';
    const EVENT_REASON_RETURN_TO_SENDER = 'RE';
    const EVENT_REASON_SHIPMENT_DELAY = 'TM';
    const EVENT_REASON_SHIPMENT_DELIVERED_TO_CUSTOMS = 'CT';

    public static function getEventCodeDictionary(): array
    {
        return [
            self::EVENT_CODE_BOOKED => 'Zlecono odbiór przesyłki',
            self::EVENT_CODE_COLLECTED => 'Przesyłka odebrana przez Schenker',
            self::EVENT_CODE_DELIVERED_TO_TERMINAL_BY_SHIPPER => 'Nadawca dostarczył przesyłkę do terminala Schenker',
            self::EVENT_CODE_DEPARTED => 'Przesyłka opuściła terminal nadania',
            self::EVENT_CODE_ARRIVED_TO_DESTINATION_TERMINAL => 'Przesyłka została dostarczona do terminalu docelowego',
            self::EVENT_CODE_CUSTOMS_CLEARANCE_INITIATED => 'Obsługa celna została rozpoczęta',
            self::EVENT_CODE_CUSTOMS_CLEARANCE_FINALISED => 'Obsługa celna została zakończona',
            self::EVENT_CODE_TO_CONSIGNEES_DISPOSAL => 'Przesyłka oczekuje na decyzję odbiorcy',
            self::EVENT_CODE_PICKED_UP_BY_CONSIGNEE => 'Przesyłka została odebrana przez Odbiorcę bezpośrednio z terminalu Schenker',
            self::EVENT_CODE_OUT_OF_DELIVERY => 'Przesyłka przekazana kierowcy do celem dostawy',
            self::EVENT_CODE_DELIVERED => 'Przesyłka została dostarczona',
            self::EVENT_CODE_TERMINAL_INVENTORY => 'Przesyłka zeskanowana na terminalu',
            self::EVENT_CODE_NOT_DELIVERED => 'Przesyłka nie została dostarczona',
            self::EVENT_CODE_EPOD_AVAILABLE => 'Elektroniczne potwierdzenie dostawy (z podpisem odbiorcy) jest dostępne',
        ];
    }

    public static function getEventReasonDictionary(): array
    {
        return [
            self::EVENT_REASON_ORDER_CANCELED => 'Zlecenie anulowano',
            self::EVENT_REASON_DELIVERY_DATE_CHANGED_BY_RECIPIENT => 'Data dostawy zmieniona przez Odbiorcę',
            self::EVENT_REASON_DELIVERY_DATE_CHANGED_BY_SENDER => 'Data dostawy zmieniona przez Nadawcę',
            self::EVENT_REASON_CLOSED => 'Zamknięte/Urlop',
            self::EVENT_REASON_COD_NOT_PAID => 'Pobranie nie zostało opłacone',
            self::EVENT_REASON_NO_DELIVERED_RECIPIENT_ERROR => 'Niedostarczono z przyczyn leżących po stronie Odbiorcy',
            self::EVENT_REASON_RECIPIENT_NOTIFIED_ABOUT_PACKAGE => 'Odbiorca powiadomiony o przesyłce',
            self::EVENT_REASON_ACT_OF_GOD => 'Siła wyższa',
            self::EVENT_REASON_INCOMPLETE_SHIPMENT => 'Przesyłka niekompletna',
            self::EVENT_REASON_INCORRECT_SHIPMENT_DATA => 'Niepoprawne dane o przesyłce',
            self::EVENT_REASON_DELAY_BY_SENDER => 'Opóźnienie po stronie Nadawcy',
            self::EVENT_REASON_DELAY_BY_CUSTOMS_PROCEDURE => 'Opóźnienie z powodu procedurę celnych',
            self::EVENT_REASON_SHIPMENT_LOST => 'Przesyłka zaginęła',
            self::EVENT_REASON_SHIPMENT_DAMAGED => 'Przesyłka uszkodzona',
            self::EVENT_REASON_DELIVERY_PLEDGED => 'Zaawizowano dostawę',
            self::EVENT_REASON_REJECTED_BY_RECIPIENT => 'Przesyłka odrzucona przez Odbiorcę',
            self::EVENT_REASON_RETURN_TO_SENDER => 'Zwrot do nadawcy',
            self::EVENT_REASON_SHIPMENT_DELAY => 'Przesyłka opóźniona',
            self::EVENT_REASON_SHIPMENT_DELIVERED_TO_CUSTOMS => 'Przesyłka dostarczona do obsługi celnej',
        ];
    }

}
