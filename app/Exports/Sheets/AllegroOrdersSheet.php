<?php
declare(strict_types=1);

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;

class AllegroOrdersSheet implements WithTitle, FromCollection
{
    private $title;
    private $data;

    public function __construct(string $title, array $data)
    {
        $this->title = $title;
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return collect($this->data);
    }

    public function title(): string
    {
        return 'Arkusz ' . $this->title;
    }
}
