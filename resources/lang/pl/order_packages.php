<?php

return [
    'title' => 'Przesyłki',
    'create' => 'Dodaj nową przesyłkę',
    'edit' => 'Edytuj przesyłkę',
    'list' => 'Wróć do listy przesyłek',
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
        'cost_for_client' => 'Koszt dla klienta',
        'cost_for_company' => 'Koszt dla firmy',
        'real_cost_for_company' => 'Realny koszt firmy',
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
        'courier_success' => 'Kurierzy zostaną zamówieni w ciągu kilku minut'
    ],
    'form' => [
        'data_template' => 'Szablon danych',
        'size_a' => 'Rozmiar A',
        'size_b' => 'Rozmiar B',
        'size_c' => 'Rozmiar C',
        'content' => 'Zawartość przesyłki',
        'shipment_date' => 'Data wysyłki',
        'delivery_date' => 'Przewidywana data dostarczenia',
        'service_courier_name' => 'Nazwa kuriera obsługującego',
        'delivery_courier_name' => 'Nazwa kuriera dostarczającego',
        'weight' => 'Waga',
        'quantity' => 'Ilość',
        'container_type' => 'Rodzaj opakowania',
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
        'cost' => 'Koszt pobrania',
        'maxStringLength' => 'Maksymalna ilość znaków w uwagach do spedycji'
    ]
];
