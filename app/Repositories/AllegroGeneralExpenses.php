<?php

namespace App\Repositories;

use App\DTO\AllegroBilling\ImportAllegroBillingDTO;
use App\Entities\AllegroGeneralExpense;

class AllegroGeneralExpenses
{
    /**
     * Delete all resources
     *
     * @return void
     */
    public static function deleteAll(): void
    {
        AllegroGeneralExpense::truncate();
    }

    public static function createFromDTO(ImportAllegroBillingDTO $dto): AllegroGeneralExpense
    {
        return AllegroGeneralExpense::create([
            'date_of_commitment_creation' => $dto->getDate(),
            'offer_name'                  => $dto->getOfferName(),
            'offer_identification'        => $dto->getOfferIdentifier(),
            'operation_type'              => $dto->getOperationType(),
            'credit'                      => $dto->getCredits(),
            'debit'                       => $dto->getCharges(),
            'balance'                     => $dto->getBalance(),
            'operation_details'           => $dto->getOperationDetails(),
        ]);
    }
}

