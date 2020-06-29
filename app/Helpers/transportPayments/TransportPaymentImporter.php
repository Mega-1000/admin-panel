<?php

namespace App\Helpers\transportPayments;

use App\Entities\OrderPackage;
use Illuminate\Support\Facades\Storage;

class TransportPaymentImporter
{
    private $columnLetter;
    private $columnNetPayment = false;
    private $columnGrossPayment = false;

    /**
     * @param integer $columnLetter
     */
    public function setColumnLetter($columnLetter)
    {
        $this->columnLetter = $columnLetter;
        return $this;
    }

    /**
     * @param integer $columnNetPayment
     */
    public function setColumnNetPayment($columnNetPayment)
    {
        $this->columnNetPayment = $columnNetPayment;
        return $this;
    }

    /**
     * @param integer $columnGrossPayment
     */
    public function setColumnGrossPayment($columnGrossPayment)
    {
        $this->columnGrossPayment = $columnGrossPayment;
        return $this;
    }

    public function import($file)
    {
        $path = Storage::path('user-files/transport/') . $file;

        if (($handle = fopen($path, "r")) === FALSE) {
            throw new \Exception('Nie można otworzyć pliku');
        }
        $errors = [];
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {
            try {
                $this->processLine($line);
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        fclose($handle);
        Storage::disk('private')->delete('transport/' . $file);
        return $errors;
    }

    private function processLine(?array $line)
    {
        if (empty($line[$this->columnLetter])) {
            return;
        }
        $package = OrderPackage::where('letter_number', $line[$this->columnLetter])->first();
        if (empty($package)) {
            throw new \Exception('Nie znaleziono paczki o liście nr: ' . $line[$this->columnLetter], 1);
        }

        $cost = -1;
        if ($this->columnGrossPayment) {
            $cost = floatval(str_replace(',', '.', $line[$this->columnGrossPayment]));
        } else if ($this->columnNetPayment) {
            $cost = round(floatval(str_replace(',', '.', $line[$this->columnNetPayment])) * 1.23, 2);
        }

        if ($cost === -1) {
            if (empty($package)) {
                throw new \Exception('Brak kosztów dla paczki nr: ' . $line[$this->columnLetter], 2);
            }
        }

        $package->real_cost_for_company = $cost;
        $package->save();
    }

}
