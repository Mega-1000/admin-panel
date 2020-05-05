# README #

Mega 1000
=========

Wymagania:
----------
- PHP > 7.2
- composer

Run
---
1. skonfigorować połączenie z bazą danych w .env
2. `composer install`
3. `php artisan migrate`
4. `php artisan db:seed`
5. ustawić uprawnienia do pliku .csv oraz dump.json dla uzytkownika `www-data`
6. dodać git hooki, by przy pullowaniu odpalały komendę `php artisan import:dump`
7. `yarn install`
8. `yarn dev`
9. wygnerować klucze RSA dla użytkownika `www-data` (lub innego który uruchamia serwer) i dodanie ich do deploy-keys w repozytorium


Aktualizacja CSV: 
-----------------
##### plik: `ImportCsvFileJob.php`
1. wyeksportować z excela plik o nazwie `Baza.csv` a następnie skopiować go do `public/storage`
2. `php artisan import:products`

