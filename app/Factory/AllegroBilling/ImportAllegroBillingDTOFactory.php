<?php

namespace App\Factory\AllegroBilling;

use App\DTO\AllegroBilling\ImportAllegroBillingDTO;
use Illuminate\Http\UploadedFile;

final class ImportAllegroBillingDTOFactory
{
    public function createFromFile(array|UploadedFile|null $file): array
    {
        if ($file === null) {
            return [];
        }

        $handle = fopen($file->getPathname(), 'r');

        fgetcsv($handle, 0, ';');

        $importAllegroBillingDTO = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $importAllegroBillingDTO[] = new ImportAllegroBillingDTO(
                $data[0] ?? null,
                $data[1] ?? null,
                $data[2] ?? null,
                $data[3] ?? null,
                $data[4] ?? null,
                $data[5] ?? null,
                $data[6] ?? null,
                $data[7] ?? null
            );
        }

        fclose($handle);

        return $importAllegroBillingDTO;
    }

    /**
     * @param array $billingEntries
     * @return array<ImportAllegroBillingDTO>
     */
    public static function createFromRestApi(array $billingEntries): array
    {
        $importAllegroBillingDTO = [];

        foreach ($billingEntries as $billingEntry) {

            $amount = floatval($billingEntry['value']['amount'] ?? 0);

            $importAllegroBillingDTO[] = new ImportAllegroBillingDTO(
                data: json_encode($billingEntry),
                offerName: $billingEntry['offer']['name'] ?? null,
                offerId: $billingEntry['offer']['id'] ?? null,
                operationType: $billingEntry['type']['name'] ?? null,
                incomeAmount: max($amount, 0),
                outcomeAmount: min($amount, 0),
                balance: $billingEntry['balance']['amount'] ?? 0,
                operationDetails: '',
            );

        }

        return $importAllegroBillingDTO;
    }
}
