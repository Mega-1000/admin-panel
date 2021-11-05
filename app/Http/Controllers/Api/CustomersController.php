<?php

namespace App\Http\Controllers\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
     * @param CustomerRepository        $customerRepository
     * @param CustomerAddressRepository $customerAddressRepository
     */
    public function __construct(
        CustomerRepository $customerRepository,
        CustomerAddressRepository $customerAddressRepository
    )
    {
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
        } catch (\Exception $e) {
            Log::error('Problem with create new customer.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    public function emailExists(Request $request, $email)
    {
        $customer = \App\Entities\Customer::where('login', $email)->first();
        return response($customer ? 1 : 0);
    }

    public function getDetails(Request $request)
    {
        $user = $request->user();
        $user->addresses;
        unset($user->password);
        unset($user->id);
        unset($user->id_from_old_db);
        unset($user->nick_allegro);
        unset($user->remember_token);
        return $user;
    }

    /**
     * Zwraca klientów do wyszukiwarki
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @author Norbert Grzechnik <grzechniknorbert@gmail.com>
     */
    public function getCustomers(Request $request): \Illuminate\Http\JsonResponse
    {
        $response = [];
        try {
            /** @var Builder $query */
            $query = $this->customerRepository
                ->select('orders.id as ordersId', 'customers.*', 'customer_addresses.*')
                ->leftJoin('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->leftJoin('orders', 'customers.id', '=', 'orders.id')
                ->where('customer_addresses.type', '=', 'STANDARD_ADDRESS');

            if (!empty($request->get('firstName'))) {
                $query->where('customer_addresses.firstname', 'like', $request->get('firstName') . '%');
            }
            if (!empty($request->get('lastName'))) {
                $query->where('customer_addresses.lastname', 'like', $request->get('lastName') . '%');
            }
            if (!empty($request->get('phone'))) {
                $query->where('customer_addresses.phone', 'like', $request->get('phone') . '%');
            }
            if (!empty($request->get('nickAllegro'))) {
                $query->where('nick_allegro', 'like', $request->get('nickAllegro') . '%');
            }
            if (!empty($request->get('email'))) {
                $query->where('login', 'like', $request->get('email') . '%');
            }

            $result = $query->get();

            if (!empty($result->all())) {
                $response['status'] = 200;
                if ($result->count() < 500) {
                    foreach ($result as $customer) {
                        $response['customers'][] = [
                            'id' => trim($customer->customer_id),
                            'login' => trim($customer->login),
                            'nickAllegro' => trim($customer->nick_allegro),
                            'firstName' => trim($customer->firstname),
                            'lastName' => trim($customer->lastname),
                            'phone' => trim($customer->phone),
                            'email' => trim($customer->login),
                            'ordersIds' => $customer->orders->pluck('id'),
                        ];
                    }
                } else {
                    $response = [
                        'errorCode' => 423,
                        'errorMessage' => 'Zbyt duża ilość wyników. Wpisz dodatkowe dane.',
                        'customers' => []
                    ];
                }
            } else {
                $response = [
                    'errorCode' => 424,
                    'errorMessage' => 'Brak klientów',
                    'customers' => []
                ];
            }
        } catch (\Exception $exception) {
            $response = [
                'errorCode' => $exception->getCode(),
                'errorMessage' => $exception->getMessage()
            ];
        }
        return response()->json($response);
    }
}
