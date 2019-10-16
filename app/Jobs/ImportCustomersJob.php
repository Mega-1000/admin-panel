<?php

namespace App\Jobs;

use App\Repositories\CustomerAddressRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Storage;

/**
 * Class ImportCustomersJob
 * @package App\Jobs
 */
class ImportCustomersJob extends Job
{

    /**
     * @var
     */
    protected $path;

    /**
     * @var
     */
    protected $customerRepository;

    /**
     * @var
     */
    protected $customerAddressRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->path = Storage::path('public/uzytkownicy.csv');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(CustomerRepository $customerRepository, CustomerAddressRepository $customerAddressRepository)
    {
        $this->customerRepository = $customerRepository;
        $this->customerAddressRepository = $customerAddressRepository;
        $handle = fopen($this->path, 'r');
        if ($handle) {
            $i = 1;
            while ($line = fgetcsv($handle, '', ';')) {
                if ($i > 1) {
                    $invoice = $line[14];
                    $delivery = $line[13];
                    $array = [
                        'id_from_old_db' => $line[0],
                        'login' => $line[4],
                        'password' => $line[5],
                        'nick_allegro' => $line[3],
                        'status' => 'ACTIVE',
                        'standard_address' => [
                            'firstname' => $line[6],
                            'lastname' => $line[7],
                            'phone' => $line[8],
                            'address' => $line[11],
                            'flat_number' => $line[12],
                            'postal_code' => $line[9],
                            'city' => $line[10],
                        ],
                        'delivery_address' => [
                            'firstname' => $line[23],
                            'lastname' => $line[24],
                            'phone' => $line[25],
                            'email' => $line[26],
                            'address' => $line[27],
                            'flat_number' => $line[28],
                            'postal_code' => $line[29],
                            'city' => $line[30],
                        ],
                        'invoice_address' => [
                            'firstname' => $line[31],
                            'lastname' => $line[32],
                            'phone' => $line[33],
                            'email' => $line[34],
                            'address' => $line[35],
                            'flat_number' => $line[36],
                            'postal_code' => $line[37],
                            'city' => $line[38],
                            'firmname' => $line[39],
                            'nip' => $line[40],
                        ]
                    ];
                    if (strlen($array['invoice_address']['firmname']) > 20) {
                        unset($array['invoice_address']['firmname']);
                    }
                    if (strlen($array['invoice_address']['nip']) > 16) {
                        unset($array['invoice_address']['firmname']);
                    }

                    $expInvoice = explode(';', $invoice);
                    $expDelivery = explode(';', $delivery);
                    foreach ($expInvoice as $key => $value) {
                        if ($value != null) {
                            switch ($key) {
                                case 0:
                                    if ($array['invoice_address']['firstname'] === null) {
                                        $array['invoice_address']['firstname'] = $value;
                                    }
                                    break;
                                case 1:
                                    if ($array['invoice_address']['lastname'] === null) {
                                        $array['invoice_address']['lastname'] = $value;
                                    }
                                    break;
                                case 2:
                                    if ($array['invoice_address']['phone'] === null) {
                                        $array['invoice_address']['phone'] = $value;
                                    }
                                    break;
                                case 3:
                                    if ($array['invoice_address']['address'] === null) {
                                        $array['invoice_address']['address'] = $value;
                                    }
                                    break;
                                case 4:
                                    if ($array['invoice_address']['flat_number'] === null) {
                                        $array['standard_address']['flat_number'] = $value;
                                    }
                                    break;
                                case 5:
                                    if ($array['invoice_address']['postal_code'] === null) {
                                        $array['invoice_address']['postal_code'] = $value;
                                    }
                                    break;
                                case 6:
                                    if ($array['invoice_address']['city'] === null) {
                                        $array['invoice_address']['city'] = $value;
                                    }
                                    break;
                                default:
                                    break;
                            };
                        }
                    }
                    foreach ($expDelivery as $key => $value) {
                        if ($value != null) {
                            switch ($key) {
                                case 0:
                                    if ($array['delivery_address']['firstname'] === null) {
                                        $array['delivery_address']['firstname'] = $value;
                                    }
                                    break;
                                case 1:
                                    if ($array['delivery_address']['lastname'] === null) {
                                        $array['delivery_address']['lastname'] = $value;
                                    }
                                    break;
                                case 2:
                                    if ($array['delivery_address']['phone'] === null) {
                                        $array['delivery_address']['phone'] = $value;
                                    }
                                    break;
                                case 3:
                                    if ($array['delivery_address']['address'] === null) {
                                        $array['delivery_address']['address'] = $value;
                                    }
                                    break;
                                case 4:
                                    if ($array['delivery_address']['flat_number'] === null) {
                                        $array['standard_address']['flat_number'] = $value;
                                    }
                                    break;
                                case 5:
                                    if ($array['delivery_address']['postal_code'] === null) {
                                        $array['delivery_address']['postal_code'] = $value;
                                    }
                                    break;
                                case 6:
                                    if ($array['delivery_address']['city'] === null) {
                                        $array['delivery_address']['city'] = $value;
                                    }
                                    break;
                                default:
                                    break;
                            };
                        }
                    }

                    $customer = $this->customerRepository->findByField('login', $array['login'])->first();
                    if (empty($customer)) {
                        $customer = $this->customerRepository->create($array);
                    }

                    if (!empty($array['standard_address'])) {
                        $this->customerAddressRepository->updateOrCreate(
                            [
                                'customer_id' => $customer->id,
                                'type' => 'STANDARD_ADDRESS',
                            ],
                            array_merge(
                                [
                                    'customer_id' => $customer->id,
                                    'type' => 'STANDARD_ADDRESS',
                                ],
                                $array['standard_address']
                            )
                        );
                    }

                    if (!empty($array['invoice_address'])) {
                        $this->customerAddressRepository->updateOrCreate(
                            [
                                'customer_id' => $customer->id,
                                'type' => 'INVOICE_ADDRESS',
                            ],
                            array_merge(
                                [
                                    'customer_id' => $customer->id,
                                    'type' => 'INVOICE_ADDRESS',
                                ],
                                $array['invoice_address']
                            )
                        );
                    }

                    if (!empty($array['delivery_address'])) {
                        $this->customerAddressRepository->updateOrCreate(
                            [
                                'customer_id' => $customer->id,
                                'type' => 'DELIVERY_ADDRESS',
                            ],
                            array_merge(
                                [
                                    'customer_id' => $customer->id,
                                    'type' => 'DELIVERY_ADDRESS',
                                ],
                                $array['delivery_address']
                            )
                        );
                    }
                }
                $i++;
                var_dump($i);
            }
        }
        var_dump(['customers' => $i]);
    }
}
