<?php

return [
    'title' => 'Zadania',
    'create' => 'Dodaj nowe zadanie',
    'list' => 'Powrót do listy zadań',
    'edit' => 'Edytuj zadanie',
    'table' => [
        'name' => 'Nazwa zadania',
        'user_id' => 'Wykonane przez',
        'order_id' => 'Numer zlecenia',
        'created_by' => 'Utworzone przez',
        'color' => 'Kolor',
        'status' => 'Status',
        'date_start' => 'Data rozpoczęcia',
        'date_end' => 'Data zakończenia',
        'created_at' => 'Utworzone w',
        'warehouse_id' => 'Magazyn'
    ],
    'form' => [
        'name' => 'Nazwa zadania',
        'user_id' => 'Wykonane przez',
        'order_id' => 'Numer zlecenia',
        'created_by' => 'Utworzone przez',
        'color' => 'Kolor',
        'status' => 'Status',
        'date_start' => 'Data rozpoczęcia',
        'date_end' => 'Data zakończenia',
        'warehouse_id' => 'Magazyn',
        'consultant_notice' => 'Opis obsługi konsultanta',
        'consultant_value' => 'Wartosć obsługi konsultanta',
        'warehouse_notice' => 'Opis obsługi magazynu',
        'warehouse_value' => 'Wartość obsługi magazynu'
    ],
    'messages' => [
        'store' => 'Zadanie zostało prawidłowo dodane',
        'store_error' => 'Zadanie nie zostało dodane',
        'delete' => 'Zadanie zostało pomyślnie usunięte',
        'update' => 'Zadanie zostało pomyślnie zaktualizowane',
        'update_error' => 'Zadanie nie zostało zaktualizowane',
        'cannot_delete_ask_warehouse' => 'Zadanie nie zostało zaktualizowane. Skontaktuj się z magazynem',
        'task_with_order_exist' => 'Już istnieje zadanie dla danego zamówienia',
    ]
];
