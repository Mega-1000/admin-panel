<?php

$labels = [
    'zaliczka deklarowana oczekujemy na zaksiegowanie deklarowanej wplaty- po wplaceniu kasujemy etykiete' => '4',
    'zaliczka zaksiegowana' => '5',
    'pozostalo do zaplaty przed wydaniem towaru' => '39',
    'zlecenie zostalo uregulowane' => '40',
    'faktura do wystawienia-po wystawieniu ksiegowosc kasuje' => '41',
    'faktura wystawiona' => '42',
    'faktura odebrana' => '43',
    'do wykonania-nastapila wplata lub doplata sprawdzic finanse i pobrania oraz pozostale parametry zlecenia po czym skasowac etykiete' => '44',
    'sprawdzono wszystkie parametry zlecenia -zglosci dalej do przyjecia produkcji bądź awizacji' => '45',
    'zgloszone do dalszej obslugi produkcji bądź awizacji' => '46',
    'zgloszone do przyjecia produkcji-magazyn po zatwierdzeniu kasuje etykiete' => '47',
    'produkcja przyjeta' => '48',
    'produkt w przygotowaniu-po wyprodukowaniu magazyn kasuje etykiete' => '49',
    'wyprodukowana' => '50',
    'wyprodukowano czesciowo' => '51',
    'wyslana do awizacji' => '52',
    'awizacja przyjeta' => '53',
    'awizacja odrzucona' => '54',
    'dialog rozpoczecie' => '55',
    'dialog czekamy na odpowiedz klienta' => '56',
    'dialog informacja od klienta' => '57',
    'dialog zakonczony' => '58',
    'reklamacja zgloszenie' => '59',
    'reklamacja czkeamy na odpowiedz klienta' => '60',
    'reklamacja informacja od klienta' => '61',
    'reklamacja zamknieta' => '62',
    'faktura do zatwierdzenia-konstultant po zatwierdzeniu kasuje' => '63',
    'faktura zatwierdzona-ksiegowosc kasuje po zaplaceniu' => '64',
    'faktura zaplacona' => '65',
    'towar wydany' => '66',
    'towar wydany czesciowo' => '67',
    'dokonczyc sprawy logistyczne-po ustaleniu skaskowac' => '68',
    'transport spedycyjny' => '69',
    'transport gielda' => '70',
    'transport hds' => '71',
    'odbior wlasny' => '72',
    'sprawy logistyczne ustalone' => '73',
    'towar przygotowany do wydania-po wydaniu skaskowac' => '74',
    'brak danych do dostawy' => '75',
    'brak danych do faktury' => '76',
    'awizacja brak odpowiedzi' => '77',
    'dialog rozpoczecie transport' => '78',
    'dialog czekamy na odpowiedz klienta transport' => '79',
    'dialog informacja od klienta transport' => '80',
    'dialog zakonczony transport' => '81',
    'dialog rozpoczecie produkcja' => '82',
    'dialog czekamy na odpowiedz klienta produkcja' => '83',
    'dialog informacja od klienta produkcja' => '84',
    'dialog zakonczony produkcja' => '85',
    'zgloszono do anulacji lp' => '86',
    'potwierdzono anulacje lp' => '87',
    'brak odpowiedzi na prosbe o anulacje lp' => '88',
    'przekazano do obslugi konsultantowi' => '89',
    'przyjete zapytanie ofertowe' => '92',
    'faktura wystawiona z odlozonym skutkiem magazynowym' => '120'
];

return [
    "promised-payment-chosen" => [ //4
        "add" => [
            $labels['zaliczka deklarowana oczekujemy na zaksiegowanie deklarowanej wplaty- po wplaceniu kasujemy etykiete'],
        ],
    ],
    "payment-received" => [ //5
        "add" => [
            $labels['zaliczka zaksiegowana'],
            $labels['do wykonania-nastapila wplata lub doplata sprawdzic finanse i pobrania oraz pozostale parametry zlecenia po czym skasowac etykiete'],
            $labels['dokonczyc sprawy logistyczne-po ustaleniu skaskowac'],
        ],
    ],
    "payment-equal-to-order-value" => [ //6
        "add" => [
            $labels['zlecenie zostalo uregulowane'],
        ],
        "remove" => [
            $labels['pozostalo do zaplaty przed wydaniem towaru'],
        ],
    ],
    "required-payment-before-unloading" => [ //6
        "add" => [
            $labels['pozostalo do zaplaty przed wydaniem towaru'],
        ],
        "remove" => [
            $labels['zlecenie zostalo uregulowane'],
        ],
    ],
    /*"invoice-taken" => [ //10
        "add" => [
            $labels['faktura odebrana'],
        ],
    ],*/
    /*"sent-to-warehouse-notification" => [ //21
        "add" => [
            $labels['wyslana do awizacji'],
        ],
    ],*/
    "warehouse-notification-accepted" => [ //22
        "add" => [
            $labels['awizacja przyjeta'],
        ],
        "remove" => [
            $labels['wyslana do awizacji'],
        ],
    ],
    "warehouse-notification-denied" => [ //23
        "add" => [
            $labels['awizacja odrzucona'],
        ],
        "remove" => [
            $labels['wyslana do awizacji'],
        ],
    ],
    "chatting-started" => [ //26
        "add" => [
            $labels['dialog rozpoczecie'],
        ],
    ],
    "chatting-sent-message-to-client" => [ //26
        "add" => [
            $labels['dialog czekamy na odpowiedz klienta'],
        ],
    ],
    "chatting-client-sent-message-to-us" => [ //26
        "add" => [
            $labels['dialog informacja od klienta'],
        ],
    ],
    "chatting-finished" => [ //26
        "add" => [
            $labels['dialog zakonczony'],
        ],
    ],
    "chatting-started-transport" => [ //26
        "add" => [
            $labels['dialog rozpoczecie transport'],
        ],
    ],
    "chatting-sent-message-to-client-transport" => [ //26
        "add" => [
            $labels['dialog czekamy na odpowiedz klienta transport'],
        ],
    ],
    "chatting-client-sent-message-to-us-transport" => [ //26
        "add" => [
            $labels['dialog informacja od klienta transport'],
        ],
    ],
    "chatting-finished-transport" => [ //26
        "add" => [
            $labels['dialog zakonczony transport'],
        ],
    ],
    "chatting-started-production" => [ //26
        "add" => [
            $labels['dialog rozpoczecie produkcja'],
        ],
    ],
    "chatting-sent-message-to-client-production" => [ //26
        "add" => [
            $labels['dialog czekamy na odpowiedz klienta produkcja'],
        ],
    ],
    "chatting-client-sent-message-to-us-production" => [ //26
        "add" => [
            $labels['dialog informacja od klienta produkcja'],
        ],
    ],
    "chatting-finished-production" => [ //26
        "add" => [
            $labels['dialog zakonczony produkcja'],
        ],
    ],
    "complaint-started" => [ //33
        "add" => [
            $labels['reklamacja zgloszenie'],
        ],
    ],
    "complaint-waiting-for-client" => [ //33
        "add" => [
            $labels['reklamacja czkeamy na odpowiedz klienta'],
        ],
    ],
    "complaint-client-responded" => [ //33
        "add" => [
            $labels['reklamacja informacja od klienta'],
        ],
    ],
    "complaint-closed" => [ //33
        "add" => [
            $labels['reklamacja zamknieta'],
        ],
    ],
    "new-file-added-to-order" => [ //40
        "add" => [
            $labels['faktura do zatwierdzenia-konstultant po zatwierdzeniu kasuje'],
        ],
    ],
    "preparing" => [ //49
        "add" => [
            $labels['produkt w przygotowaniu-po wyprodukowaniu magazyn kasuje etykiete'],
        ],
    ],
    "factored" => [ //50
        "add" => [
            $labels['wyprodukowana'],
        ],
    ],
    "all-shipments-went-out" => [ //54
        "add" => [
            $labels['towar wydany'],
        ],
    ],
    /*"partial-shipments-went-out" => [ //54
        "add" => [
            $labels['towar wydany czesciowo'],
        ],
    ],*/
    "missing-delivery-address" => [
        "add" => [
            $labels['brak danych do dostawy'],
        ]
    ],
    "added-delivery-address" => [
        "remove" => [
            $labels['brak danych do dostawy'],
        ]
    ],
    "new-mail-from-shipping-company" => [
        /*"add" => [
            $labels[''],
        ]*/
    ],
    "new-order-created" => [
        "add" => [
            $labels['przyjete zapytanie ofertowe'],
        ]
    ],
    "consultant-changed" => [
        "add" => [
            $labels['przekazano do obslugi konsultantowi'],
        ]
    ],
    "list" => $labels
];
