<?php

namespace App\Console\Commands;

use App\Entities\ContainerType;
use App\Enums\CourierName;
use App\Exceptions\SoapException;
use App\Exceptions\SoapParamsException;
use App\Services\SchenkerService;
use Exception;
use Illuminate\Console\Command;

class GetSchenkerPackageDictionaryCommand extends Command
{
    protected $signature = 'schenker:pull_package_dictionary';

    protected $description = 'Pulling package dictionary from Schenker API';

    /**
     * @throws SoapException
     * @throws SoapParamsException
     * @throws Exception
     */
    public function handle(): bool
    {
        $response = SchenkerService::getPackageDictionary();
        if (array_key_exists('packageDictionary', $response) && count($response['packageDictionary'])) {
            $dictionaryPositions = $response['packageDictionary'];
            $schenkerTypesCollection = ContainerType::where('shipping_provider', CourierName::DB_SCHENKER)->get();
            $newPositions = 0;
            foreach ($dictionaryPositions as $packageType) {
                $existingPosition = $schenkerTypesCollection->where('symbol', '=', $packageType['packCode'])->first();
                if ($existingPosition === null) {
                    $newContainerType = new ContainerType();
                    $newContainerType->shipping_provider = CourierName::DB_SCHENKER;
                    $newContainerType->symbol = $packageType['packCode'];
                    $newContainerType->name = $packageType['packName'];
                    $newContainerType->additional_informations = $this->mapFieldsToDescriptions($packageType);
                    $newContainerType->save();
                    $newPositions++;
                }
                $schenkerTypesCollection = $schenkerTypesCollection->filter(function ($item) use ($packageType) {
                    return $item->symbol !== $packageType['packCode'];
                });
            }
            $toRemove = $schenkerTypesCollection->pluck('id');
            ContainerType::whereIn('id', $toRemove)->delete();
            $this->info('Nowych pozycji dodanych do systemu: ' . $newPositions);
            $this->info('Pozycji usuniętych, nie aktualnych w bazie DB: ' . count($toRemove));
            return true;
        }
        $this->alert('Brak danych po stronie Schenker');
        return false;
    }

    private function mapFieldsToDescriptions($mappingElement): array
    {
        $maps = [
            "heightWarning" => "Wysokość ostrzeżenie",
            "heightMax" => "Maksymalna wysokość",
            "heightDefault" => "Domyślna wysokość",
            "height" => "Wysokość",
            "widthWarning" => "Szerokość ostrzeżenie",
            "widthMax" => "Maksymalna szerokość",
            "widthDefault" => "Domyślna szerokość",
            "width" => "Szerokość",
            "lengthWarning" => "Długość ostrzeżenie",
            "lengthMax" => "Maksymalna długość",
            "lengthDefault" => "Długość domyślna",
            "length" => "Długość",
            "weightWarning" => "Waga ostrzeżenie",
            "weightMax" => "Waga maksymalna",
            "weightDefault" => "Waga domyślna",
            "weight" => "Waga",
            "m3Min" => "Objętość minimalna",
            "m3Max" => "Objętość maksymalna",
            "m3Default" => "Objętość domyślna",
            "isPalet" => "Czy paleta",
            "palPlace" => "palPlace",
            "productsAllowed" => "Produkty dozwolone",
        ];

        $mappedFields = [];
        foreach ($maps as $systemKey => $friendlyName) {
            $mappedFields[$friendlyName] = $mappingElement[$systemKey] ?? 'N/D';
        }

        return $mappedFields;
    }
}
