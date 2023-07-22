<?php

namespace App\Factory\AllegroBilling;

use Illuminate\Http\UploadedFile;
use App\DTO\AllegroBilling\ImportAllegroBillingDTO;

final class ImportAllegroBillingDTOFactory
{
    public function createFromFile(array|UploadedFile|null $file): array
    {
        if ($file === null) {
            return [];
        }

        $handle = fopen($file->getPathname(), 'r');

        fgetcsv($handle, 0, ';');

        $dtos = [];

        while (($data = fgetcsv($handle, 0, ';')) !== false) {
            $dtos[] = new ImportAllegroBillingDTO(
                $data[0] ?? null,
                $dwata[1] ?? null,
                $data[2] ?? null,
                $data[3] ?? null,
                $data[4] ?? null,
                $data[5] ?? null,
                $data[6] ?? null,
                $data[7] ?? null
            );
        }

        fclose($handle);

        return $dtos;
    }
}
