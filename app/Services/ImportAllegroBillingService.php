<?php

namespace App\Services;

use App\DTO\AllegroBilling\ImportAllegroBillingDTO;
use App\Entities\AllegroGeneralExpense;
use App\Repositories\AllegroGeneralExpenses;

class ImportAllegroBillingService
{

    /**
     * Import File from Allegro Billing
     *
     * @param array<ImportAllegroBillingDTO> $data
     */
    public function import(array $data): void
    {
        foreach ($data as $dto) {
            $this->importSingle($dto);
        }
    }

    /**
     * Import Single Allegro Billing
     *
     * @param ImportAllegroBillingDTO $dto
     * @return void
     */
    private function importSingle(ImportAllegroBillingDTO $dto): void
    {
        AllegroGeneralExpenses::createFromDTO($dto);


    }
}
