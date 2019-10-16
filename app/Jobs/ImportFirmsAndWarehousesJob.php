<?php

namespace App\Jobs;

use App\Repositories\EmployeeRepository;
use App\Repositories\FirmAddressRepository;
use App\Repositories\FirmRepository;
use App\Repositories\WarehouseAddressRepository;
use App\Repositories\WarehousePropertyRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImportFirmsAndWarehousesJob
 * @package App\Jobs
 */
class ImportFirmsAndWarehousesJob extends Job
{
    /**
     * @var
     */
    protected $path;

    /**
     * @var
     */
    protected $firmRepository;

    /**
     * @var
     */
    protected $firmAddressRepository;

    /**
     * @var
     */
    protected $warehouseRepository;

    /**
     * @var
     */
    protected $warehouseAddressRepository;

    /**
     * @var
     */
    protected $warehousePropertyRepository;

    protected $employeeRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->path = Storage::path('public/klienci_wkonawcy.csv');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(
        FirmRepository $firmRepository,
        FirmAddressRepository $firmAddressRepository,
        WarehouseRepository $warehouseRepository,
        WarehouseAddressRepository $warehouseAddressRepository,
        WarehousePropertyRepository $warehousePropertyRepository,
        EmployeeRepository $employeeRepository
    ) {
        $this->firmRepository = $firmRepository;
        $this->firmAddressRepository = $firmAddressRepository;
        $this->warehouseRepository = $warehouseRepository;
        $this->warehouseAddressRepository = $warehouseAddressRepository;
        $this->warehousePropertyRepository = $warehousePropertyRepository;
        $this->employeeRepository = $employeeRepository;

        $handle = fopen($this->path, 'r');
        if ($handle) {
            $i = 1;
            while ($line = fgetcsv($handle, '', ';')) {
                if ($i > 1) {
                    if ($i == 3493 || $i == 3494) {
                        continue;
                    }
                    if ($line[1] == null) {
                        $arrayFirm = [
                            'name' => $line[5],
                            'short_name' => $line[6],
                            'symbol' => $line[4] != null ? $line[4] : $line[0],
                            'email' => $line[12],
                            'nip' => $line[14],
                            'account_number' => $line[15],
                            'phone' => $line[16],
                            'notices' => $line[3],
                            'status' => 'ACTIVE',
                            'id_from_old_db' => $line[0]
                        ];
                        if ($line[10] != null) {
                            $geo = explode(',', $line[10]);
                        } else {
                            $geo[0] = null;
                            $geo[1] = null;
                        }
                        $arrayFirmAddress = [
                            'city' => $line[11],
                            'latitude' => $geo[0],
                            'longitude' => $geo[1],
                            'flat_number' => $line[8] != null ? $line[8] : null,
                            'address' => $line[7],
                            'postal_code' => $line[9]
                        ];

                        $arrayWarehouse = [
                            'symbol' => $line[39] != null ? $line[39] : $line[4],
                            'status' => 'ACTIVE'
                        ];
                        if($arrayWarehouse['symbol'] == null){
                            $arrayWarehouse['symbol'] = $line[0];
                        }
                        $arrayWarehouseAddress = [
                            'address' => $line[40],
                            'warehouse_number' => $line[41],
                            'postal_code' => $line[42],
                            'city' => $line[43]
                        ];
                        $arrayWarehouseProperty = [
                            'firstname' => $line[45],
                            'lastname' => $line[46],
                            'phone' => $line[44],
                            'comments' => $line[47],
                            'additional_comments' => $line[48],
                            'email' => $line[13]
                        ];
                        $arrayEmployee = [
                            'email' => $line[25],
                            'firstname' => $line[26],
                            'lastname' => $line[27],
                            'phone' => $line[24],
                            'job_position' => 'CONSULTANT',
                            'comments' => $line[31],
                            'additional_comments' => $line[32],
                            'postal_code' => $line[28],
                            'radius' => $line[29],
                            'status' => 'ACTIVE'
                        ];
                        foreach ($arrayWarehouseAddress as $key => $value) {
                            if ($key == 'address' && $value == null) {
                                $value = $arrayFirmAddress[$key];
                                $arrayWarehouseAddress[$key] = $value;
                            }
                            if ($key == 'warehouse_number' && $value == null) {
                                $value = $arrayFirmAddress['flat_number'];
                                $arrayWarehouseAddress[$key] = $value;
                            }
                            if ($key == 'postal_code' && $value == null) {
                                $value = $arrayFirmAddress[$key];
                                $arrayWarehouseAddress[$key] = $value;
                            }
                            if ($key == 'city' && $value == null) {
                                $value = $arrayFirmAddress[$key];
                                $arrayWarehouseAddress[$key] = $value;
                            }
                        }

                        $firm = $this->firmRepository->create($arrayFirm);
                        $firmAddress = $this->firmAddressRepository->create(array_merge([
                            'firm_id' => $firm->id,
                        ], $arrayFirmAddress));
                        $warehouse = $this->warehouseRepository->create(array_merge(['firm_id' => $firm->id],
                            $arrayWarehouse));
                        $this->warehouseAddressRepository->create(array_merge(['warehouse_id' => $warehouse->id],
                            $arrayWarehouseAddress));
                        $this->warehousePropertyRepository->create(array_merge(['warehouse_id' => $warehouse->id],
                            $arrayWarehouseProperty));
                        $this->employeeRepository->create(array_merge([
                            'firm_id' => $firm->id,
                            'warehouse_id' => $warehouse->id
                        ], $arrayEmployee));
                    } else {
                        if ($line[10] != null) {
                            $geo = explode(',', $line[10]);
                        } else {
                            $geo[0] = null;
                            $geo[1] = null;
                        }
                        $arrayFirmAddress = [
                            'city' => $line[11],
                            'latitude' => $geo[0],
                            'longitude' => $geo[1],
                            'flat_number' => $line[8] != null ? $line[8] : null,
                            'address' => $line[7],
                            'postal_code' => $line[9]
                        ];

                        $arrayWarehouse = [
                            'symbol' => $line[39] != null ? $line[39] : $line[4],
                            'status' => 'ACTIVE'
                        ];
                        if($arrayWarehouse['symbol'] == null){
                            $arrayWarehouse['symbol'] = $line[0];
                        }
                        $arrayWarehouseAddress = [
                            'address' => $line[40],
                            'warehouse_number' => $line[41],
                            'postal_code' => $line[42],
                            'city' => $line[43]
                        ];
                        foreach ($arrayWarehouseAddress as $key => $value) {
                            if ($key == 'address' && $value == null) {
                                $value = $arrayFirmAddress[$key];
                                $arrayWarehouseAddress[$key] = $value;
                            }
                            if ($key == 'warehouse_number' && $value == null) {
                                $value = $arrayFirmAddress['flat_number'];
                                $arrayWarehouseAddress[$key] = $value;
                            }
                            if ($key == 'postal_code' && $value == null) {
                                $value = $arrayFirmAddress[$key];
                                $arrayWarehouseAddress[$key] = $value;
                            }
                            if ($key == 'city' && $value == null) {
                                $value = $arrayFirmAddress[$key];
                                $arrayWarehouseAddress[$key] = $value;
                            }
                        }

                        $arrayWarehouseProperty = [
                            'firstname' => $line[45],
                            'lastname' => $line[46],
                            'phone' => $line[44],
                            'comments' => $line[47],
                            'additional_comments' => $line[48],
                            'email' => $line[13]
                        ];
                        $arrayEmployee = [
                            'email' => $line[25],
                            'firstname' => $line[26],
                            'lastname' => $line[27],
                            'phone' => $line[24],
                            'job_position' => 'CONSULTANT',
                            'comments' => $line[31],
                            'additional_comments' => $line[32],
                            'postal_code' => $line[28],
                            'radius' => $line[29],
                            'status' => 'ACTIVE'
                        ];
                        $firm = $this->firmRepository->findByField('id_from_old_db', $line[1]);
                        $warehouse = $this->firmRepository->create(array_merge(['firm_id' => $firm->first->id->id],
                            $arrayWarehouse));
                        $this->warehouseAddressRepository->create(array_merge(['warehouse_id' => $warehouse->id],
                            $arrayWarehouseAddress));
                        $this->warehousePropertyRepository->create(array_merge(['warehouse_id' => $warehouse->id],
                            $arrayWarehouseProperty));
                        $this->employeeRepository->create(array_merge([
                            'firm_id' => $firm->id,
                            'warehouse_id' => $warehouse->id
                        ], $arrayEmployee));
                    }
                }
                var_dump($i);
                $i++;
            }
            var_dump(['warehouse' => $i]);
        }
    }
}
