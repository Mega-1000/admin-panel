<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Customers\StoreCustomerRequest;
use App\Http\Controllers\Controller;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Log;

class CustomersController extends Controller
{
    use ApiResponsesTrait;

    /** @var CustomerRepository */
    protected $customerRepository;

    /** @var CustomerAddressRepository */
    protected $customerAddressRepository;

    /**
     * CustomersController constructor.
     * @param CustomerRepository $customerRepository
     * @param CustomerAddressRepository $customerAddressRepository
     */
    public function __construct(
        CustomerRepository $customerRepository,
        CustomerAddressRepository $customerAddressRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerAddressRepository = $customerAddressRepository;
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            $data = $request->validated();
            $customer = $this->customerRepository->findByField('login', $data['login'])->first();
            if (empty($customer)) {
                $customer = $this->customerRepository->create($data);
            }

            if (!empty($data['standard_address'])) {
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
                        $data['standard_address']
                    )
                );
            }

            if (!empty($data['invoice_address'])) {
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
                        $data['invoice_address']
                    )
                );
            }

            if (!empty($data['delivery_address'])) {
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
                        $data['delivery_address']
                    )
                );
            }

            return $this->createdResponse();
        } catch (\Exception $e){
            Log::error('Problem with create new customer.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }
}
