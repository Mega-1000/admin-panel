# README #

Mega 1000

[Wymagania:]
- PHP > 7
- composer

[Run]
1. skonfigorować połączenie z bazą danych w .env
2. `composer install`
3. `php artisan migrate`
4. `php artisan db:seed`

[Aktualizacja CSV]: ImportCsvFileJob
1. wyeksportować z excela plik o nazwie `Baza.csv` a następnie skopiować go do `public/storage`
2. `php artisan import:product` || `php artisan import:products`
