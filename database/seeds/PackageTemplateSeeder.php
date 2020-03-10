<?php

use Illuminate\Database\Seeder;
use App\Entities\PackageTemplate;

class PackageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $deliveryDataTemplates = [
            'inpost-kurier-70x50x50-30kg' => [
                'name' => 'Inpost kurier 70x50x50 30kg',
                'service_courier_name' => 'INPOST',
                'delivery_courier_name' => 'INPOST',
                'size_a' => 700,
                'size_b' => 500,
                'size_c' => 500,
                'quantity' => 1,
                'shape' => '',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'inpost-kurier-niestandard-70x50x50-30kg' => [
                'name' => 'Inpost kurier niestandard 70x50x50 30kg',
                'service_courier_name' => 'INPOST',
                'delivery_courier_name' => 'INPOST',
                'size_a' => 700,
                'size_b' => 500,
                'size_c' => 500,
                'quantity' => 1,
                'shape' => 'niestandard',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'inpost-paczkomat-41x38x64-25kg' => [
                'name' => 'Inpost paczkomat 41x38x64 25kg',
                'service_courier_name' => 'INPOST',
                'delivery_courier_name' => 'INPOST',
                'size_a' => 410,
                'size_b' => 380,
                'size_c' => 640,
                'quantity' => 1,
                'shape' => '',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'dpd-standard-100x43x41-31-5kg' => [
                'name' => 'DPD standard 100x43x41 31,5kg',
                'service_courier_name' => 'DPD',
                'delivery_courier_name' => 'DPD',
                'size_a' => 100,
                'size_b' => 43,
                'size_c' => 41,
                'quantity' => 1,
                'shape' => 'standard',
                'weight' => 31.5,
                'container_type' => 'PACZ'
            ],
            'dpd-niestandard-100x43x41-31-5kg' => [
                'name' => 'DPD niestandard 100x43x41 31,5kg',
                'service_courier_name' => 'DPD',
                'delivery_courier_name' => 'DPD',
                'size_a' => 100,
                'size_b' => 43,
                'size_c' => 41,
                'quantity' => 1,
                'shape' => 'niestandard',
                'weight' => 31.5,
                'container_type' => 'PACZ'
            ],
            'dluzyca,250X26X26, 250X36X20 itp, AXBXC/6000<31,5' => [
                'name' => 'DPD dluzyca 250x26x26 lub 200x31x30 31,5kg',
                'service_courier_name' => 'DPD',
                'delivery_courier_name' => 'DPD',
                'size_a' => 260,
                'size_b' => 26,
                'size_c' => 26,
                'quantity' => 1,
                'shape' => 'niestandard',
                'weight' => 31.5,
                'container_type' => 'DLUZYCA'
            ],
            'poczta-polska-paczka-139x60x50-30kg' => [
                'name' => 'Poczta polska paczka 139x60x50 30kg',
                'service_courier_name' => 'A',
                'delivery_courier_name' => 'POCZTA_POLSKA_E24',
                'size_a' => 139,
                'size_b' => 60,
                'size_c' => 50,
                'quantity' => 1,
                'shape' => 'standard',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'poczta-polska-paleta-60x80x200-1000kg' => [
                'name' => 'Poczta polska paleta 60x80x200 1000kg',
                'service_courier_name' => 'POCZTEX',
                'delivery_courier_name' => 'POCZTEX',
                'size_a' => 60,
                'size_b' => 120,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'POLPALETA'
            ],
            'poczta-polska-paleta-80x120x200-1000kg' => [
                'name' => 'Poczta polska paleta 80x120x200 1000kg',
                'service_courier_name' => 'POCZTEX',
                'delivery_courier_name' => 'POCZTEX',
                'size_a' => 80,
                'size_b' => 120,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'EUR'
            ],
            'poczta-polska-paleta-100x120x200-1000kg' => [
                'name' => 'Poczta polska paleta 100x120x200 1000kg',
                'service_courier_name' => 'POCZTEX',
                'delivery_courier_name' => 'POCZTEX',
                'size_a' => 100,
                'size_b' => 120,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'poczta-polska-paleta-ok-130x150x200-1000kg' => [
                'name' => 'Poczta polska paleta ok 130x150x200 1000kg',
                'service_courier_name' => 'POCZTEX',
                'delivery_courier_name' => 'POCZTEX',
                'size_a' => 130,
                'size_b' => 150,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'jas-paleta-60x80x220-1000kg' => [
                'name' => 'jas paleta 60x80x220 1000kg',
                'service_courier_name' => 'JAS',
                'delivery_courier_name' => 'JAS',
                'size_a' => 60,
                'size_b' => 120,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'jas-paleta-80x120x220-1000kg' => [
                'name' => 'jas paleta 80x120x220 1000kg',
                'service_courier_name' => 'JAS',
                'delivery_courier_name' => 'JAS',
                'size_a' => 80,
                'size_b' => 120,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'EUR'
            ],
            'jas-paleta-100x120x220-1000kg' => [
                'name' => 'jas paleta 100x120x220 1000kg',
                'service_courier_name' => 'JAS',
                'delivery_courier_name' => 'JAS',
                'size_a' => 100,
                'size_b' => 120,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'jas-paleta-ok-130x150x220-1000kg' => [
                'name' => 'jas paleta ok 130x150x220 1000kg',
                'service_courier_name' => 'JAS',
                'delivery_courier_name' => 'JAS',
                'size_a' => 130,
                'size_b' => 150,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'gielda-paczka-70x50x50-30kg' => [
                'name' => 'Giełda paczka 70x50x50 30kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 70,
                'size_b' => 50,
                'size_c' => 50,
                'quantity' => 1,
                'shape' => '',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'gielda-paczka-niestandard-70x50x50-30kg' => [
                'name' => 'Giełda paczka niestandard 70x50x50 30kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 70,
                'size_b' => 50,
                'size_c' => 50,
                'quantity' => 1,
                'shape' => 'niestandard',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'gielda-standard-100x43x41-31-5kg' => [
                'name' => 'Giełda standard 100x43x41 31,5kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 100,
                'size_b' => 43,
                'size_c' => 41,
                'quantity' => 1,
                'shape' => 'standard',
                'weight' => 31.5,
                'container_type' => 'PACZ'
            ],
            'gielda-niestandard-100x43x41-31-5kg' => [
                'name' => 'Giełda niestandard 100x43x41 31,5kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 100,
                'size_b' => 43,
                'size_c' => 41,
                'quantity' => 1,
                'shape' => 'niestandard',
                'weight' => 31.5,
                'container_type' => 'PACZ'
            ],
            'gielda-dluzyca-250x25x24-lub-200x31x30-31-5kg' => [
                'name' => 'Giełda dluzyca 250x25x24 lub 200x31x30 31,5kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 100,
                'size_b' => 43,
                'size_c' => 41,
                'quantity' => 1,
                'shape' => 'niestandard',
                'weight' => 31.5,
                'container_type' => 'DLUZYCA'
            ],
            'gielda-paczka-139x60x50-30kg' => [
                'name' => 'Giełda paczka 139x60x50 30kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 139,
                'size_b' => 60,
                'size_c' => 50,
                'quantity' => 1,
                'shape' => 'standard',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'gielda-paleta-60x80x200-1000kg' => [
                'name' => 'Giełda paleta 60x80x200 1000kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 60,
                'size_b' => 120,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'POLPALETA'
            ],
            'gielda-paleta-80x120x200-1000kg' => [
                'name' => 'Giełda paleta 80x120x200 1000kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 80,
                'size_b' => 120,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'EUR'
            ],
            'gielda-paleta-100x120x200-1000kg' => [
                'name' => 'Giełda paleta 100x120x200 1000kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 100,
                'size_b' => 120,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'gielda-paleta-ok-130x150x200-1000kg' => [
                'name' => 'Giełda paleta ok 130x150x200 1000kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 130,
                'size_b' => 150,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'gielda-paleta-60x80x220-1000kg' => [
                'name' => 'Giełda paleta 60x80x220 1000kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 60,
                'size_b' => 120,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'gielda-paleta-80x120x220-1000kg' => [
                'name' => 'Giełda paleta 80x120x220 1000kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 80,
                'size_b' => 120,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'EUR'
            ],
            'gielda-paleta-100x120x220-1000kg' => [
                'name' => 'Giełda paleta 100x120x220 1000kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 100,
                'size_b' => 120,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'gielda-paleta-ok-130x150x220-1000kg' => [
                'name' => 'Giełda paleta ok 130x150x220 1000kg',
                'service_courier_name' => 'GIELDA',
                'delivery_courier_name' => 'GIELDA',
                'size_a' => 130,
                'size_b' => 150,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'odbior-osobisty-paczka-70x50x50-30kg' => [
                'name' => 'Odbiór osobisty paczka 70x50x50 30kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 70,
                'size_b' => 50,
                'size_c' => 50,
                'quantity' => 1,
                'shape' => '',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'odbior-osobisty-paczka-niestandard-70x50x50-30kg' => [
                'name' => 'Odbiór osobisty paczka niestandard 70x50x50 30kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 70,
                'size_b' => 50,
                'size_c' => 50,
                'quantity' => 1,
                'shape' => 'niestandard',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'odbior-osobisty-standard-100x43x41-31-5kg' => [
                'name' => 'Odbiór osobisty standard 100x43x41 31,5kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 100,
                'size_b' => 43,
                'size_c' => 41,
                'quantity' => 1,
                'shape' => 'standard',
                'weight' => 31.5,
                'container_type' => 'PACZ'
            ],
            'odbior-osobisty-niestandard-100x43x41-31-5kg' => [
                'name' => 'Odbiór osobisty niestandard 100x43x41 31,5kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 100,
                'size_b' => 43,
                'size_c' => 41,
                'quantity' => 1,
                'shape' => 'niestandard',
                'weight' => 31.5,
                'container_type' => 'PACZ'
            ],
            'odbior-osobisty-dluzyca-250x25x24-lub-200x31x30-31-5kg' => [
                'name' => 'Odbiór osobisty dluzyca 250x25x24 lub 200x31x30 31,5kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 100,
                'size_b' => 43,
                'size_c' => 41,
                'quantity' => 1,
                'shape' => 'niestandard',
                'weight' => 31.5,
                'container_type' => 'DLUZYCA'
            ],
            'odbior-osobisty-paczka-139x60x50-30kg' => [
                'name' => 'Odbiór osobisty paczka 139x60x50 30kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 139,
                'size_b' => 60,
                'size_c' => 50,
                'quantity' => 1,
                'shape' => 'standard',
                'weight' => 30,
                'container_type' => 'PACZ'
            ],
            'odbior-osobisty-paleta-60x80x200-1000kg' => [
                'name' => 'Odbiór osobisty paleta 60x80x200 1000kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 60,
                'size_b' => 120,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'POLPALETA'
            ],
            'odbior-osobisty-paleta-80x120x200-1000kg' => [
                'name' => 'Odbiór osobisty paleta 80x120x200 1000kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 80,
                'size_b' => 120,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'EUR'
            ],
            'odbior-osobisty-paleta-100x120x200-1000kg' => [
                'name' => 'Odbiór osobisty paleta 100x120x200 1000kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 100,
                'size_b' => 120,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'odbior-osobisty-paleta-ok-130x150x200-1000kg' => [
                'name' => 'Odbiór osobisty paleta ok 130x150x200 1000kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 130,
                'size_b' => 150,
                'size_c' => 200,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'odbior-osobisty-paleta-60x80x220-1000kg' => [
                'name' => 'Odbiór osobisty paleta 60x80x220 1000kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 60,
                'size_b' => 120,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'odbior-osobisty-paleta-80x120x220-1000kg' => [
                'name' => 'Odbiór osobisty paleta 80x120x220 1000kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 80,
                'size_b' => 120,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'EUR'
            ],
            'odbior-osobisty-paleta-100x120x220-1000kg' => [
                'name' => 'Odbiór osobisty paleta 100x120x220 1000kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 100,
                'size_b' => 120,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
            'odbior-osobisty-paleta-ok-130x150x220-1000kg' => [
                'name' => 'Odbiór osobisty paleta ok 130x150x220 1000kg',
                'service_courier_name' => 'ODBIOR_OSOBISTY',
                'delivery_courier_name' => 'ODBIOR_OSOBISTY',
                'size_a' => 130,
                'size_b' => 150,
                'size_c' => 220,
                'quantity' => 1,
                'shape' => '',
                'weight' => 1000,
                'container_type' => 'INNA'
            ],
        ];
        foreach ($deliveryDataTemplates as $deliveryDataTemplate) {
            $packageTemplate = PackageTemplate::create([
                'name' => $deliveryDataTemplate['name'],
                'service_courier_name' => $deliveryDataTemplate['service_courier_name'],
                'delivery_courier_name' => $deliveryDataTemplate['delivery_courier_name'],
                'sizeA' => $deliveryDataTemplate['size_a'],
                'sizeB' => $deliveryDataTemplate['size_b'],
                'sizeC' => $deliveryDataTemplate['size_c'],
                'shape' => $deliveryDataTemplate['shape'],
                'weight' => $deliveryDataTemplate['weight'],
                'container_type' => $deliveryDataTemplate['container_type'],  
                'notice_max_lenght' => 50
            ]);
        }
    }
}
