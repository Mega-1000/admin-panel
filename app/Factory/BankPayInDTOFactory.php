<?php

namespace App\Factory;

use App\DTO\PayInImport\BankPayInDTO;
use App\Helpers\PdfCharactersHelper;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BankPayInDTOFactory
{
    /**
     * Create array of dtos from csv file
     *
     * @param mixed $file
     * @return array<BankPayInDTO>
     * @throws Exception
     */
    public static function fromFile(mixed $file): array
    {
        $header = NULL;
        $data = [];

        if (($handle = fopen($file, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 5000, ';')) !== FALSE) {
                if (count(array_filter($row)) < 5) {
                    continue;
                }

                if (!$header) {
                    foreach ($row as &$headerName) {
                        if (!empty($headerName)) {
                            $headerName = str_replace('#', '', iconv('ISO-8859-2', 'UTF-8', $headerName));
                            $headerName = Str::snake(PdfCharactersHelper::changePolishCharactersToNonAccented($headerName));
                        }
                    }
                    $header = $row;
                } else {
                    foreach ($row as &$text) {
                        $text = iconv('ISO-8859-2', 'UTF-8', $text);
                    }
                    $data[] = array_combine($header, $row);
                }
            }
            fclose($handle);
        }


        return self::create($data);
    }

    /**
     * create array of dtos from array
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public static function create(array $data): array
    {
        $response = [];

        foreach ($data as $value) {
            $dto = new BankPayInDTO(
                orderId: $value['orderId'] ?? null,
                data: $value['data'] ?? null,
                message: $value['message'] ?? null,
                kwota: $value['kwota'] ?? null,
                operation_type: $value['operation_type'] ?? '',
                opis_operacji: $value['opis_operacji'] ?? null,
                tytul: $value['tytul'] ?? null,
                data_ksiegowania: $value['data_ksiegowania'] ?? null
            );

            $response[] = $dto;
        }

        return $response;
    }
}
