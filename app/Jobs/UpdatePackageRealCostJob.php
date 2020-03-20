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
        $inpostPackages = array();
        $pocztexPackages = array();
        $dpdPackages = array();
        foreach ($this->orderPackageRepository as $package) {
            if ($package->service_courier_name == 'INPOST') {
                $inpostPackages[] = $package;
            } 
            if ($package->service_courier_name == 'POCZTEX') {
                $pocztexPackages[] = $package;
            } 
            if ($package->service_courier_name == 'DPD') {
                $dpdPackages[] = $package;
            } 
        }
        if (!empty($inpostPackages)) {
            $spec = fopen('specyfikacjaInpost.csv', 'r');
            while (($csvLine = fgetcsv($spec, 1000, ";")) !== FALSE) {
                foreach ($inpostPackages as $inpostPackage) {
                    if ($inpostPackage->letter_number == $csvLine[2] ){
                        $inpostPackage->real_cost_for_company = $csvLine[7];
                        $inpostPackage->save();
                    }
                }
            }   
        }
        if (!empty($pocztexPackages)) {
            $spec = fopen('specyfikacjaPocztex.csv','r');
            while (($csvLine = fgetcsv($spec, 1000, ",")) !== FALSE) {
                
                foreach ($pocztexPackages as $pocztexPackage) {
                    if ($pocztexPackage->letter_number == $csvLine[0] ){
                        $pocztexPackage->real_cost_for_company = $csvLine[17];
                        $pocztexPackage->save();
                    }
                }
            }   
        }
        if (!empty($dpdPackages)) {
            $spec = fopen('specyfikacjaDpd.csv','r');
            while (($csvLine = fgetcsv($spec, 1000, ",")) !== FALSE) {
                foreach ($dpdPackages as $dpdPackage) {
                    if ($dpdPackage->letter_number == $csvLine[11] ){
                        $dpdPackage->real_cost_for_company = $csvLine[14];
                        $dpdPackage->save();
                    }
                }
            }   
        }
        
    }


}
