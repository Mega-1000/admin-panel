<?php

namespace App\Jobs;

use App\Domains\DelivererPackageImport\PriceFormatter;
use App\Repositories\OrderPackageRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Entities\OrderPackage;
use Illuminate\Support\Facades\Storage;
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
        $pathInpost = Storage::path('user-files/costs/INPOST.csv');
        if (!empty($inpostPackages) && file_exists($pathInpost)) {
            $spec = fopen($pathInpost, 'r');
            while (($csvLine = fgetcsv($spec, 1000, ",")) !== FALSE) {
                foreach ($inpostPackages as $inpostPackage) {
                    if ($inpostPackage->letter_number == $csvLine[2]) {
                        $inpostPackage->status = "DELIVERED";
                        $inpostPackage->save();

                        $this->saveOrderPackageRealCostForCompany($inpostPackage, $csvLine[7]);
                    }
                }
            }
        }
        $pathPP = Storage::path('user-files/costs/POCZTAPOLSKA.csv');
        if (!empty($pocztexPackages) && file_exists($pathPP)) {
            $spec = fopen($pathPP, 'r');
            while (($csvLine = fgetcsv($spec, 1000, ",")) !== FALSE) {
                foreach ($pocztexPackages as $pocztexPackage) {
                    if ($pocztexPackage->letter_number == $csvLine[0]) {
                        $pocztexPackage->status = "DELIVERED";
                        $pocztexPackage->save();

                        $this->saveOrderPackageRealCostForCompany($pocztexPackage, $csvLine[17]);
                    }
                }
            }
        }
        $pathDpd = Storage::path('user-files/costs/DPD.csv');
        if (!empty($dpdPackages) && file_exists($pathDpd)) {
            $spec = fopen($pathDpd, 'r');
            while (($csvLine = fgetcsv($spec, 1000, ",")) !== FALSE) {
                foreach ($dpdPackages as $dpdPackage) {
                    if ($dpdPackage->letter_number == $csvLine[11]) {
                        $dpdPackage->status = "DELIVERED";
                        $dpdPackage->save();

                        $this->saveOrderPackageRealCostForCompany($dpdPackage, $csvLine[14]*1.23);
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
                case "ALLEGRO-INPOST":
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

    private function saveOrderPackageRealCostForCompany(OrderPackage $orderPackage, string $cost): Model
    {
        return $orderPackage->realCostForCompany()->create([
            'order_package_id' => $orderPackage->id,
            'cost' => PriceFormatter::asAbsolute(
                PriceFormatter::fromString($cost)
            ),
        ]);
    }
}
