<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\Customer;
use App\Entities\CustomerPayments;
use App\Entities\Order;
use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use TCG\Voyager\Models\Role;
use App\Helpers\Helper;
use Illuminate\Http\Request;

/**
 * Class CustomersController.
 *
 * @package namespace App\Http\Controllers;
 */
class CustomersController extends Controller
{
    /**
     * @var CustomerRepository
     */
    protected $repository;

    /**
     * @var CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * @var
     */
    protected $roleName;

    /**
     * CustomersController constructor.
     *
     * @param CustomerRepository $repository
     */
    public function __construct(CustomerRepository $repository, CustomerAddressRepository $customerAddressRepository)
    {
        $this->repository = $repository;
        $this->customerAddressRepository = $customerAddressRepository;
    }


    public function changeLoginOrPassword(Request $request, $id)
    {
        try {
            $this->overrideContact($request, $id);
        } catch (\Exception $exception) {
            Log::error("Can't change login or password",
                ['message' => $exception->getMessage(),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine()]);
            return back()->with([
                'message' => __('voyager.generic.update_failed'),
                'alert-type' => 'error',
            ]);
        }

        return back()->with([
            'message' => __('customers.message.update'),
            'alert-type' => 'success',
        ]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $arr = [];
        $arr[] = collect(['item' => 2]);
        $arr[] = collect(['item' => 3]);


        $collect = collect($arr);

        $role = Role::find(Auth::user()->role_id);
        $roleName = $role->name;
        $this->roleName = $roleName;
        //pobieramy widzialności dla danego moduły oraz użytkownika
        $visibilities = ColumnVisibility::getVisibilities(ColumnVisibility::getModuleId('customers'));
        foreach($visibilities as $key => $row)
        {
            $visibilities[$key]->show = json_decode($row->show,true);
            $visibilities[$key]->hidden = json_decode($row->hidden,true);
        }
        return view('customers.index', compact('roleName','visibilities'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        $roleName = $role->name;
        $this->roleName = $roleName;
        return view('customers.create', compact('roleName'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CustomerCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CustomerCreateRequest $request)
    {
        $customer = $this->repository->create($request->all());

        $this->customerAddressRepository->create([
            'customer_id' => $customer->id,
            'type' => 'STANDARD_ADDRESS',
            'firstname' => $request->standard_firstname,
            'lastname' => $request->standard_lastname,
            'firmname' => $request->standard_firmname,
            'nip' => $request->standard_nip,
            'phone' => $request->standard_phone,
            'address' => $request->standard_address,
            'flat_number' => $request->standard_flat_number,
            'city' => $request->standard_city,
            'postal_code' => $request->standard_postal_code,
            'email' => $request->standard_email
        ]);

        $this->customerAddressRepository->create([
            'customer_id' => $customer->id,
            'type' => 'INVOICE_ADDRESS',
            'firstname' => $request->invoice_firstname,
            'lastname' => $request->invoice_lastname,
            'firmname' => $request->invoice_firmname,
            'nip' => $request->invoice_nip,
            'phone' => $request->invoice_phone,
            'address' => $request->invoice_address,
            'flat_number' => $request->invoice_flat_number,
            'city' => $request->invoice_city,
            'postal_code' => $request->invoice_postal_code,
            'email' => $request->invoice_email
        ]);

        $this->customerAddressRepository->create([
            'customer_id' => $customer->id,
            'type' => 'DELIVERY_ADDRESS',
            'firstname' => $request->delivery_firstname,
            'lastname' => $request->delivery_lastname,
            'firmname' => $request->delivery_firmname,
            'nip' => $request->delivery_nip,
            'phone' => $request->delivery_phone,
            'address' => $request->delivery_address,
            'flat_number' => $request->delivery_flat_number,
            'city' => $request->delivery_city,
            'postal_code' => $request->delivery_postal_code,
            'email' => $request->delivery_email
        ]);

        return redirect()->route('customers.edit', ['id' => $customer->id])->with([
            'message' => __('customers.message.create'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $customer = $this->repository->find($id);
        $customerAddressStandard = $this->customerAddressRepository->findWhere([
            'customer_id' => $customer->id,
            'type' => 'STANDARD_ADDRESS'
        ]);
        $customerAddressInvoice = $this->customerAddressRepository->findWhere([
            'customer_id' => $customer->id,
            'type' => 'INVOICE_ADDRESS'
        ]);
        $customerAddressDelivery = $this->customerAddressRepository->findWhere([
            'customer_id' => $customer->id,
            'type' => 'DELIVERY_ADDRESS'
        ]);
        $role = Role::find(Auth::user()->role_id);
        $roleName = $role->name;
        $this->roleName = $roleName;
        return view('customers.edit',
            compact('customer', 'customerAddressStandard', 'customerAddressInvoice', 'customerAddressDelivery',
                'roleName'));
    }

    /**
     * @param CustomerUpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CustomerUpdateRequest $request, $id)
    {
        $customer = $this->repository->find($id);
        if (empty($customer)) {
            abort(404);
        }
        if ($request->login !== null) {
            $request->request->remove('login');
        }
        $dataToStore = $request->all();
        if ($dataToStore['password'] !== null) {
            $dataToStore['password'] = bcrypt($dataToStore['password']);
        } else {
            unset($dataToStore['password']);
        }

        $this->repository->update($dataToStore, $customer->id);

        $customerAddressStandard = $this->customerAddressRepository->findWhere([
            'customer_id' => $customer->id,
            'type' => 'STANDARD_ADDRESS'
        ]);
        if (Helper::checkRole('customers', 'standard_firstname') === true) {
            $customerAddressStandard->first->id->firstname = $request->standard_firstname;
        }
        if (Helper::checkRole('customers', 'standard_lastname') === true) {
            $customerAddressStandard->first->id->lastname = $request->standard_lastname;
        }
        if (Helper::checkRole('customers', 'standard_firmname') === true) {
            $customerAddressStandard->first->id->firmname = $request->standard_firmname;
        }
        if (Helper::checkRole('customers', 'standard_nip') === true) {
            $customerAddressStandard->first->id->nip = $request->standard_nip;
        }
        if (Helper::checkRole('customers', 'standard_phone') === true) {
            $customerAddressStandard->first->id->phone = $request->standard_phone;
        }
        if (Helper::checkRole('customers', 'standard_address') === true) {
            $customerAddressStandard->first->id->address = $request->standard_address;
        }
        if (Helper::checkRole('customers', 'standard_flat_number') === true) {
            $customerAddressStandard->first->id->flat_number = $request->standard_flat_number;
        }
        if (Helper::checkRole('customers', 'standard_city') === true) {
            $customerAddressStandard->first->id->city = $request->standard_city;
        }
        if (Helper::checkRole('customers', 'standard_postal_code') === true) {
            $customerAddressStandard->first->id->postal_code = $request->standard_postal_code;
        }
        if (Helper::checkRole('customers', 'standard_email') === true) {
            $customerAddressStandard->first->id->email = $request->standard_email;
        }
        $customerAddressStandard->first->id->update();
        $customerAddressInvoice = $this->customerAddressRepository->findWhere([
            'customer_id' => $customer->id,
            'type' => 'INVOICE_ADDRESS'
        ]);
        if (Helper::checkRole('customers', 'invoice_firstname') === true) {
            $customerAddressInvoice->first->id->firstname = $request->invoice_firstname;
        }
        if (Helper::checkRole('customers', 'invoice_lastname') === true) {
            $customerAddressInvoice->first->id->lastname = $request->invoice_lastname;
        }
        if (Helper::checkRole('customers', 'invoice_firmname') === true) {
            $customerAddressInvoice->first->id->firmname = $request->invoice_firmname;
        }
        if (Helper::checkRole('customers', 'invoice_nip') === true) {
            $customerAddressInvoice->first->id->nip = $request->invoice_nip;
        }
        if (Helper::checkRole('customers', 'invoice_phone') === true) {
            $customerAddressInvoice->first->id->phone = $request->invoice_phone;
        }
        if (Helper::checkRole('customers', 'invoice_address') === true) {
            $customerAddressInvoice->first->id->address = $request->invoice_address;
        }
        if (Helper::checkRole('customers', 'invoice_flat_number') === true) {
            $customerAddressInvoice->first->id->flat_number = $request->invoice_flat_number;
        }
        if (Helper::checkRole('customers', 'invoice_city') === true) {
            $customerAddressInvoice->first->id->city = $request->invoice_city;
        }
        if (Helper::checkRole('customers', 'invoice_postal_code') === true) {
            $customerAddressInvoice->first->id->postal_code = $request->invoice_postal_code;
        }
        if (Helper::checkRole('customers', 'invoice_email') === true) {
            $customerAddressInvoice->first->id->email = $request->invoice_email;
        }
        $customerAddressInvoice->first->id->update();


        $customerAddressDelivery = $this->customerAddressRepository->findWhere([
            'customer_id' => $customer->id,
            'type' => 'DELIVERY_ADDRESS'
        ]);
        if (Helper::checkRole('customers', 'delivery_firstname') === true) {
            $customerAddressDelivery->first->id->firstname = $request->delivery_firstname;
        }
        if (Helper::checkRole('customers', 'delivery_lastname') === true) {
            $customerAddressDelivery->first->id->lastname = $request->delivery_lastname;
        }
        if (Helper::checkRole('customers', 'delivery_firmname') === true) {
            $customerAddressDelivery->first->id->firmname = $request->delivery_firmname;
        }
        if (Helper::checkRole('customers', 'delivery_nip') === true) {
            $customerAddressDelivery->first->id->nip = $request->delivery_nip;
        }
        if (Helper::checkRole('customers', 'delivery_phone') === true) {
            $customerAddressDelivery->first->id->phone = $request->delivery_phone;
        }
        if (Helper::checkRole('customers', 'delivery_address') === true) {
            $customerAddressDelivery->first->id->address = $request->delivery_address;
        }
        if (Helper::checkRole('customers', 'delivery_flat_number') === true) {
            $customerAddressDelivery->first->id->flat_number = $request->delivery_flat_number;
        }
        if (Helper::checkRole('customers', 'delivery_city') === true) {
            $customerAddressDelivery->first->id->city = $request->delivery_city;
        }
        if (Helper::checkRole('customers', 'delivery_postal_code') === true) {
            $customerAddressDelivery->first->id->postal_code = $request->delivery_postal_code;
        }
        if (Helper::checkRole('customers', 'delivery_email') === true) {
            $customerAddressDelivery->first->id->email = $request->delivery_email;
        }
        $customerAddressDelivery->first->id->update();

        return redirect()->back()->with([
            'message' => __('customers.message.update'),
            'alert-type' => 'success'
        ]);

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (empty($deleted)) {
            return redirect()->back()->with([
                'message' => __('customers.message.not_delete'),
                'alert-type' => 'error'
            ]);
        }

        return redirect()->back()->with([
            'message' => __('customers.message.delete'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeStatus($id)
    {
        $customer = $this->repository->find($id);
        if (empty($customer)) {
            abort(404);
        }
        $dataToStore = [];
        $dataToStore['status'] = $customer['status'] === 'ACTIVE' ? 'PENDING' : 'ACTIVE';
        $this->repository->update($dataToStore, $customer->id);

        return redirect()->back()->with([
            'message' => __('customers.message.change_status'),
            'alert-type' => 'success'
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable(Request $request)
    {
        $data = $request->all();
        $collection = $this->prepareCollection($data);
        $countFiltred = $this->countFiltered($data);

        $count = $this->repository->with('addresses')->whereHas('addresses', function ($query) {
            $query->where('type', '=', 'STANDARD_ADDRESS');
        })->all();

        $count = count($count);


        return DataTables::of($collection)->with(['recordsFiltered' => $countFiltred])->skipPaging()->setTotalRecords($count)->make(true);
    }


    /**
     * @return mixed
     */
    public function prepareCollection($data)
    {
        $query = \DB::table('customers')
            ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
            ->where('customer_addresses.type', '=', 'STANDARD_ADDRESS');

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
            }
        }

        if ($data['search']['value']) {
            foreach ($data['columns'] as $column) {
                $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
            }
        }

        $collection = $query
            ->limit($data['length'])->offset($data['start'])
            ->get();

        return $collection;
    }

    /**
     * @return mixed
     */
    public function countFiltered($data)
    {
        $query = \DB::table('customers')
            ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
            ->where('customer_addresses.type', '=', 'STANDARD_ADDRESS');

        foreach ($data['columns'] as $column) {
            if ($column['searchable'] == 'true' && !empty($column['search']['value'])) {
                $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
            }
        }

        if ($data['search']['value']) {
            foreach ($data['columns'] as $column) {
                $query->where($column['name'], 'LIKE', "%{$column['search']['value']}%");
            }
        }

        $collection = $query
            ->count();

        return $collection;
    }

    /**
     * @param Request $request
     * @param $id
     */
    private function overrideContact(Request $request, $id): void
    {
        $order = Order::findOrFail($request->order_id);
        $customer = Customer::findOrFail($id);
        if ($request->login) {
            $customer->login = $request->login;
        }
        if ($request->phone) {
            $customer->password = $customer->generatePassword($request->phone);
        }

        $customer->addresses->map(function ($address) use ($request) {
            if ($request->phone) {
                $address->phone = $request->phone;
            }
            if ($request->login) {
                $address->email = $request->login;
            }
            $address->save();
        });

        $invoice = $order->getInvoiceAddress();
        if ($request->invoice_phone && $request->phone) {
            $invoice->phone = $request->phone;
        }
        if ($request->invoice_email && $request->login) {
            $invoice->email = $request->login;
        }
        $invoice->save();
        $delivery = $order->getDeliveryAddress();
        if ($request->delivery_phone && $request->phone) {
            $delivery->phone = $request->phone;
        }
        if ($request->delivery_email && $request->login) {
            $delivery->email = $request->login;
        }
        $delivery->save();
        $customer->save();
    }

    /**
     * Action for customer payments
     *
     * @param Customer $customer Klient
     */
    public function payments(Customer $customer)
    {
//        $customerPayments = CustomerPayments::find($id);
        dd($customer);

        return view('customers.payments'
//            compact('customer', 'customerAddressStandard', 'customerAddressInvoice', 'customerAddressDelivery',
//                'roleName')
        );
    }
}




