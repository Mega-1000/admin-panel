<?php
declare(strict_types=1);

namespace App\Exports;

use App\Enums\AllegroExcel\SheetNames;
use App\Exports\Sheets\AllegroOrdersSheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use \Illuminate\Support\Collection;

class OrdersAllegroExport implements FromCollection, WithMultipleSheets
{
    public $orderData;
    public $allegroPayments;
    public $clientPayments;

    public function __construct(array $orderData, array $allegroPayments, array $clientPayments)
    {
        $this->orderData = $orderData;
        $this->allegroPayments = $allegroPayments;
        $this->clientPayments = $clientPayments;
    }

    public function collection(): Collection
    {
        return collect([$this->orderData, $this->allegroPayments, $this->clientPayments]);
    }

    public function sheets(): array
    {
        $sheetsTitle = [
            SheetNames::getDescription(SheetNames::ORDER_DATA),
            SheetNames::getDescription(SheetNames::ALLEGRO_PAYMENTS),
            SheetNames::getDescription(SheetNames::CLIENT_PAYMENTS),
        ];

        return $this->collection()->map(function($item, $key) use ($sheetsTitle) {
           return new AllegroOrdersSheet($sheetsTitle[$key], $item);
        })->toArray();
    }
}
