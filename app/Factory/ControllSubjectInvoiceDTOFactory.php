<?php

namespace App\Factory;

use App\DTO\ControllSubjectInvoice\ControllSubjectInvoiceDTO;
use Illuminate\Http\UploadedFile;

class ControllSubjectInvoiceDTOFactory
{
    private static array $columnMap = [
        'K' => 'k',
        'Data zakończenia dostawy' => 'deliveryEndDate',
        'Data magazynowa' => 'warehouseDate',
        'Data wystawienia' => 'issueDate',
        'Liczba komentarzy' => 'commentsCount',
        'Numer' => 'number',
        'Wartość' => 'value',
        'Flaga' => 'flag',
        'Nabywca - Symbol' => 'buyerSymbol',
        'Uwagi' => 'notes',
        'Nabywca' => 'buyer',
        'Netto' => 'net',
        'Wartość VAT' => 'vatValue',
        'Nabywca - E-mail' => 'buyerEmail',
        'S' => 's',
        'T' => 't',
        'Brutto' => 'gross',
        'Pozostało do zapłaty' => 'remainingPayment',
        'Waluta' => 'currency',
        'Kategoria' => 'category',
        'Tytuł' => 'title',
        'Forma płatności' => 'paymentMethod',
        'Nabywca - VATIN' => 'buyerVATIN',
        'Dokument księgowy' => 'accountingDocument',
        'MPP' => 'mpp',
        'Oryginały zamówień' => 'originalOrders',
        'Zamówienia' => 'orders',
        'Flaga - Komentarz' => 'flagComment',
        'Do nieistniejącego' => 'toNonExistent'
    ];

    private static function mapDataToDTOColumns(array $data): array
    {
        $mappedData = [];

        foreach (self::$columnMap as $csvColumn => $dtoColumn) {
            $mappedData[$dtoColumn] = $data[$csvColumn] ?? null;
        }

        return $mappedData;
    }

    public static function createFromCsvFile(UploadedFile $file): array
    {
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        $headers = array_shift($csvData);

        $dtos = [];

        foreach ($csvData as $row) {
            $mappedData = self::mapDataToDTOColumns(array_combine($headers, $row));
            $dtos[] = new ControllSubjectInvoiceDTO($mappedData);
        }

        return $dtos;
    }
}

