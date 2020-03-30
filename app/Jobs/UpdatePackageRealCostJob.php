<?php

namespace App\Jobs;

use App\Repositories\OrderPackageRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Entities\OrderPackage;
use Carbon\Carbon;

class UpdatePackageRealCostJob implements ShouldQueue {

    public function __construct() {
        $this->orderPackageRepository = OrderPackage::all();
    }

    public function handle() {
        $sortedPackages = $this->sortPackages();
        $inpostPackages = $sortedPackages['INPOST'];
        $pocztexPackages = $sortedPackages['POCZTEX'];
        $dpdPackages = $sortedPackages['DPD'];
        $this->updateCost($inpostPackages, $pocztexPackages, $dpdPackages);
    }

    public function updateCost($inpostPackages, $pocztexPackages, $dpdPackages) {
        if (!empty($inpostPackages)) {
            $spec = fopen('specyfikacjaInpost.csv', 'r');
            while (($csvLine = fgetcsv($spec, 1000, ";")) !== FALSE) {
                foreach ($inpostPackages as $inpostPackage) {
                    if ($inpostPackage->letter_number == $csvLine[2]) {
                        $inpostPackage->real_cost_for_company = $csvLine[7];
                        $inpostPackage->save();
                    }
                }
            }
        }
        if (!empty($pocztexPackages)) {
            $spec = fopen('specyfikacjaPocztaPolska.csv', 'r');
            while (($csvLine = fgetcsv($spec, 1000, ",")) !== FALSE) {
                foreach ($pocztexPackages as $pocztexPackage) {
                    if ($pocztexPackage->letter_number == $csvLine[0]) {
                        $pocztexPackage->real_cost_for_company = $csvLine[17];
                        $pocztexPackage->save();
                    }
                }
            }
        }
        if (!empty($dpdPackages)) {
            $spec = fopen('specyfikacjaDpd.csv', 'r');
            while (($csvLine = fgetcsv($spec, 1000, ",")) !== FALSE) {
                foreach ($dpdPackages as $dpdPackage) {
                    if ($dpdPackage->letter_number == $csvLine[11]) {
                        $dpdPackage->real_cost_for_company = $csvLine[14];
                        $dpdPackage->save();
                    }
                }
            }
        }
    }

    public function sortPackages() {
        $inpostPackages = array();
        $pocztexPackages = array();
        $dpdPackages = array();
        foreach ($this->orderPackageRepository as $package) {
            switch ($package->service_courier_name) {
                case "INPOST":
                    $inpostPackages[] = $package;
                    break;
                case "POCZTEX":
                    $pocztexPackages[] = $package;
                    break;
                case "DPD":
                    $dpdPackages[] = $package;
                    break;
            }
        }
        $sortedPackages['INPOST'] = $inpostPackages;
        $sortedPackages['POCZTEX'] = $pocztexPackages;
        $sortedPackages['DPD'] = $dpdPackages;
        return $sortedPackages;
    }

}
