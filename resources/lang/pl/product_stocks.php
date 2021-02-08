<?php
return [
    'title' => 'Stany magazynowe',
    'list' => 'Lista stanów magazynowych',
    'edit' => 'Edytuj stan magazynowy',
    'packet_create' => 'Stwórz pakiet produktu',
    'table' => [
        'name' => 'Nazwa',
        'symbol' => 'Symbol',
        'url' => 'Adres URL',
        'status' => 'Status',
        'manufacturer' => 'Producent',
        'quantity' => 'Stan magazynowy',
        'min_quantity' => 'Minimalny stan magazynowy',
        'unit' => 'Jednostka',
        'start_quantity' => 'Początkowy stan magazynowy',
        'number_on_a_layer' => 'Ilość na warstwie',
        'created_at' => 'Utworzone w',
        'net_purchase_price_commercial_unit' => 'Cena zakupowa netto jednostki handlowej',
        'net_purchase_price_commercial_unit_after_discounts' => 'Cena zakupowa netto jednostki handlowej po rabatach',
        'net_special_price_commercial_unit' => 'Cena specjalna netto zakupu jednostki handlowej',
        'net_purchase_price_basic_unit' => 'Cena zakupowa netto jednostki podstawowej',
        'net_purchase_price_basic_unit_after_discounts' => 'Cena zakupowa netto po rabatach',
        'net_special_price_basic_unit' => 'Cena specjalna netto zakupu jednostki podstawowej',
        'net_purchase_price_calculated_unit' => 'Cena zakupowa netto jednostki obliczeniowej',
        'net_purchase_price_calculated_unit_after_discounts' => 'Cena zakupowa netto jednostki obliczeniowej po rabatach',
        'net_special_price_calculated_unit' => 'Cena specjalna netto jednostki obliczeniowej',
        'gross_purchase_price_aggregate_unit' => 'Cena zakupowa brutto jednostki zbiorczej',
        'gross_purchase_price_aggregate_unit_after_discounts' => 'Cena zakupowa brutto jednostki zbiorczej po rabatach',
        'gross_special_price_aggregate_unit' => 'Cena specjalna brutto jednostki zbiorczej',
        'gross_purchase_price_the_largest_unit' => 'Cena zakupowa brutto jednostki największej',
        'gross_purchase_price_the_largest_unit_after_discounts' => 'Cena zakupowa brutto jednostki największej po rabatach',
        'gross_special_price_the_largest_unit' => 'Cena specjalna brutto jednostki największej',
        'net_selling_price_commercial_unit' => 'Cena sprzedaży netto jednostki handlowej',
        'net_selling_price_basic_unit' => 'Cena sprzedaży netto jednostki podstawowej',
        'net_selling_price_calculated_unit' => 'Cena sprzedaży netto jednostki obliczeniowej',
        'net_selling_price_aggregate_unit' => 'Cena sprzedaży netto jednostki zbiorczej',
        'net_selling_price_the_largest_unit' => 'Cena sprzedaży netto jednostki największej',
        'discount1' => 'Rabat 1',
        'discount2' => 'Rabat 2',
        'discount3' => 'Rabat 3',
        'bonus1' => 'Bonus 1',
        'bonus2' => 'Bonus 2',
        'bonus3'=> 'Bonus 3',
        'gross_price_of_packing' => 'Cena brutto opakowania',
        'table_price' => 'Cena tabelaryczna',
        'vat' => 'Podatek VAT',
        'additional_payment_for_milling' => 'Dopłata za frezowanie',
        'coating' => 'Narzut',
        'positions' => 'Pozycje produktu',
        'packet_create' => 'Stwórz pakiet',
    ],
    'form' => [
        'name' => 'Nazwa',
        'symbol' => 'Symbol',
        'url' => 'Adres url',
        'status' => 'Status',
        'manufacturer' => 'Producent',
        'quantity' => 'Stan magazynowy',
        'min_quantity' => 'Minimalny stan magazynowy',
        'unit' => 'Jednostka',
        'start_quantity' => 'Początkowy stan magazynowy',
        'number_on_a_layer' => 'Ilość na warstwie',
        'lane' => 'Alejka',
        'bookstand' => 'Regał',
        'shelf' => 'Półka',
        'position' => 'Pozycja',
        'created_at' => 'Utworzone w',
        'active' => 'Aktywny',
        'pending' => 'Oczekujący',
        'select_position' => 'Wybierz pozycję na magazynie',
        'packet_quantity' => 'Ilość pakietów do utworzenia',
        'packet_product_quantity' => 'Ilość produktu w pakiecie',
        'packet_name' => 'Nazwa pakietu',
        'buttons' => [
            'general' => 'Główne',
            'stocks' => 'Stan magazynowy',
            'positions' => 'Pozycje w magazynie',
            'logs' => 'Historia zmian',
            'position_create' => 'Dodaj pozycję w magazynie',
            'add_product' => 'Dodaj produkt do pakietu',
        ],
    ],
    'message' => [
        'packet_store' => 'Pakiet produktu został pomyślnie utworzony',
        'change_status' => 'Status produktu został pomyślnie zaktualizowany',
        'update' => 'Stan magazynowy produktu został pomyślnie zaktualizowany',
        'error_quantity' => 'Stan magazynowy danej pozycji nie może być niższy niż 0',
        'position_quantity_is_smaller' => 'Pozycja główna produktu nie posiada wystarczającej ilości produktu',
        'position_not_exists' => 'Pozycja główna produktu nie istnieje. Utwórz nową.',
        'packet_product_store' => 'Pomyślnie dodano produkt do pakietu',
    ]
];
