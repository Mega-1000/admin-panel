<?php
namespace App\Exports\Sheets;

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

    public function collection()
    {
        return collect($this->data);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Arkusz ' . $this->title;
    }
}
