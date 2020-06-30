<?php

return [
    'title' => 'Przesyłki',
    'create' => 'Dodaj nową przesyłkę',
    'edit' => 'Edytuj przesyłkę',
    'list' => 'Wróć do listy przesyłek',
    'content_create' => 'Dodaj nowy typ zawartości przesyłki',
    'content_edit' => 'Edytuj typ zawartości przesyłki',
    'content_list' => 'Lista typów zawartości przesyłki',
    'contents' => 'Typy zawartości przesyłki',
    'container_create' => 'Dodaj nowy rodzaj przesyłki',
    'container_edit' => 'Edytuj rodzaj przesyłki',
    'container_list' => 'Lista rodzajów przesyłki',
    'containers' => 'Rodzaje przesyłki',
    'packing_create' => 'Dodaj nowy typ opakowania przesyłki',
    'packing_edit' => 'Edytuj typ opakowania przesyłki',
    'packing_list' => 'Lista typów opakowania przesyłki',
    'packings' => 'Typy opakowania przesyłki',
    'table' => [
        'number' => 'Numer',
        'size_a' => 'Rozmiar A',
        'size_b' => 'Rozmiar B',
        'size_c' => 'Rozmiar C',
        'shipment_date' => 'Data wysyłki',
        'delivery_date' => 'Data dostarczenia',
        'delivery_courier_name' => 'Nazwa kuriera dostarczającego',
        'service_courier_name' => 'Nazwa kuriera obsługującego',
        'weight' => 'Waga',
        'quantity' => 'Ilość',
        'container_type' => 'Rodzaj opakowania',
        'shape' => 'Kształt',
        'cash_on_delivery' => 'Kwota za pobraniem',
        'notices' => 'Uwagi',
        'status' => 'Status',
        'new' => 'Nowa',
        'sending' => 'Towar wydany',
        'waiting_for_sending' => 'Oczekuje na wydanie',
        'delivered' => 'Dostarczone',
        'cancelled' => 'Anulowana',
        'sending_number' => 'Numer nadania',
        'letter_number' => 'Numer listu przewozowego',
        'cost_for_client' => 'Koszt dla klienta brutto',
        'cost_for_company' => 'Koszt dla firmy brutto',
        'real_cost_for_company' => 'Realny koszt firmy brutto',
        'created_at' => 'Utworzone w',
        'actions' => 'Akcje',
        'waiting_for_cancelled' => 'Oczekuje na anulację',
        'reject_cancelled' => 'Anulacja odrzucona',
    ],
    'message' => [
        'store' => 'Paczka została dodana pomyślnie!',
        'update' => 'Paczka została zaktualizowana pomyślnie!',
        'delete' => 'Paczka została usunięta pomyślnie!',
        'request_for_cancelled_package' => 'Wysłano prośbę o anulowanie przesyłki',
        'protocol_error' => 'Protokół jest pusty, brak dzisiejszych paczek',
        'courier_error' => 'Został wybrany niewłaściwy kurier',
        'courier_success' => 'Kurierzy zostaną zamówieni w ciągu kilku minut',
        'content_update' => 'Typ zawartości przesyłki zaktualizowany pomyślnie!',
        'content_store' => 'Typ zawartości przesyłki dodany pomyślnie!',
        'content_delete' => 'Typ zawartości przesyłki usunięty pomyślnie!',
        'container_update' => 'Rodzaj przesyłki zaktualizowany pomyślnie!',
        'container_store' => 'Rodzaj przesyłki dodany pomyślnie!',
        'container_delete' => 'Rodzaj przesyłki usunięty pomyślnie!',
        'packing_update' => 'Typ opakowania przesyłki zaktualizowany pomyślnie!',
        'packing_store' => 'Typ opakowania przesyłki dodany pomyślnie!',
        'packing_delete' => 'Typ opakowania przesyłki usunięty pomyślnie!',
        'package_error' => 'Podana paczka nie istnieje, lub szablon został usunięty'
    ],
    'form' => [
        'delivery' => 'Id dostawy z SELLO (tr_DeliveryId)',
        'deliverer' => 'Id dostawcy z SELLO (tr_DelivererId)',
        'displayed_name' => 'Nazwa paczki wyświetlana użytkownikowi',
        'data_template' => 'Szablon danych',
        'size_a' => 'Rozmiar A (Najdłuższy wymiar w paczce lub najkrótszy wymiar przy palecie)',
        'size_b' => 'Rozmiar B (Drugi pod wzlgędem wartości wymiar paczki oraz drugi wymiar palety ale nie wysokość)',
        'size_c' => 'Rozmiar C (Nakrótszy bok paczki bądź wysokość palety)',
        'content' => 'Zawartość przesyłki',
        'shipment_date' => 'Data wysyłki',
        'delivery_date' => 'Przewidywana data dostarczenia',
        'service_courier_name' => 'Nazwa kuriera obsługującego',
        'delivery_courier_name' => 'Nazwa kuriera dostarczającego',
        'weight' => 'Waga',
        'quantity' => 'Ilość',
        'container_type' => 'Rodzaj przesyłki',
        'packing_type' => 'Rodzaj opakowania',
        'shape' => 'Kształt',
        'cash_on_delivery' => 'Kwota pobrania',
        'notices' => 'Uwagi do spedycji',
        'status' => 'Status',
        'sending_number' => 'Numer nadania',
        'letter_number' => 'Numer listu przewozowego',
        'cost_for_client' => 'Koszt transportu dla klienta',
        'cost_for_company' => 'Koszt transportu dla firmy',
        'real_cost_for_company' => 'Realny koszt transportu dla firmy',
        'chosen_data_template_for_edit' => 'Nazwa wybranego szablonu danych',
        'force_shipment' => 'Wymuś wysłanie (mimo przekroczonej godziny przyjmowania zleceń)',
        'accept_time' => 'Godzina przyjmowania zleceń (Gdy ta godzina zostanie przekroczona wyświetli się pod datą przesyłki informacja z pola poniżej)',
        'max_time' => 'Godzina graniczna wysyłania przesyłki (Gdy ta godzina zostanie przekroczona wyświetli się pod datą przesyłki informacja z pola poniżej)',
        'real_weight' => 'Wyliczona waga paczki (Rzeczywista waga paczki według naszego systemu która zostaje przepisana z systemu dzielenia na LP calego zlecenia)',
        'max_weight' => 'Maksymalna waga przesyłki dla wybranego szablonu (potrzebne do plecaka)',
        'volume_factor' => 'Współczynnik objętości (potrzebne do plecaka)',
        'list_order' => 'Miejsce szablonu na liście',
        'accept_time_info' => 'Informacja dotycząca Godzin Zleceń',
        'max_time_info' => 'Informacja dotycząca Godziny Granicznej',
        'force_shipment1' => 'Wymuś wysłanie (mimo przekroczonej godziny przyjmowania zleceń)',
        'template_symbol' => 'Symbol',
        'content_type_name' => 'Nazwa typu zawartości przesyłki (Na przykład materiały budowlane)',
        'container_type_name' => 'Nazwa rodzaju przesyłki (Na przykład PACZKA)',
        'packing_type_name' => 'Nazwa typu opakowania przesyłki (Na przykład KARTON)',
        'symbol' => 'Symbol szablonu',
        'status_type' => [
            'delivered' => 'Dostarczone',
            'cancelled' => 'Anulowane',
            'new' => 'Nowa',
            'sending' => 'Towar wydany',
            'waiting_for_sending' => 'Oczekuje na wydanie',
            'waiting_for_cancelled' => 'Oczekuje na anulację',
            'reject_cancelled' => 'Anulacja odrzucona',
        ],
        'buttons' => [
            'details' => 'Szczegóły',
            'payments' => 'Płatności',
            'tasks' => 'Zadania',
            'messages' => 'Wiadomości',
            'packages' => 'Przesyłki'
        ],
        'cancelled_package' => 'Wyślij prośbę o anulowanie przesyłki',
        'maxWeight' => 'Maksymalna waga Paczki u wybranego kuriera',
        'weightVolume' => 'Objętość wagowa [cm3]',
        'cod_cost' => 'Koszt pobrania dla klienta',
        'cod_cost_for_us' => 'Koszt pobrania dla nas',
        'maxStringLength' => 'Maksymalna ilość znaków w uwagach do spedycji'
    ]
];
