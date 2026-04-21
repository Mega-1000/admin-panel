<?php

namespace App\Http\Controllers;

use App\Entities\ColumnVisibility;
use App\Entities\Customer;
use App\Entities\CustomerAddress;
use App\Entities\EmailSending;
use App\Entities\Order;
use App\Helpers\Helper;
use App\Http\Requests\CustomerCreateRequest;
use App\Http\Requests\CustomerUpdateRequest;
use App\MailReport;
use App\Repositories\CustomerAddressRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use TCG\Voyager\Models\Role;
use Yajra\DataTables\Facades\DataTables;

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
     * @param CustomerAddressRepository $customerAddressRepository
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
        } catch (Exception $exception) {
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
     * @return Factory|View
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
        foreach ($visibilities as $key => $row) {
            $row->show = json_decode($row->show, true);
            $row->hidden = json_decode($row->hidden, true);
        }
        return view('customers.index', compact('roleName', 'visibilities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CustomerCreateRequest $request
     *
     * @return RedirectResponse
     *
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
     * @return Factory|View
     */
    public function create(): View
    {
        $role = Role::find(Auth::user()->role_id);
        $roleName = $role->name;

        $this->roleName = $roleName;

        return view('customers.create', compact('roleName'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return View
     */
    public function edit(int $id): View
    {
        $customer = Customer::query()->findOrFail($id);
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

        $emails = MailReport::where('email', $customer->login)->get();

        return view('customers.edit', compact(
                'customer',
                'customerAddressStandard',
                'customerAddressInvoice',
                'customerAddressDelivery',
                'roleName',
                'emails',
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
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
     * @return RedirectResponse
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
     * @param CustomerUpdateRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(CustomerUpdateRequest $request, $id)
    {
        /** @var Customer $customer */
        $customer = Customer::query()->findOrFail($id);
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

        $customerAddressStandard = $customer->standardAddress();

        if (Helper::checkRole('customers', 'standard_firstname') === true) {
            $customerAddressStandard->firstname = $request->standard_firstname;
        }
        if (Helper::checkRole('customers', 'standard_lastname') === true) {
            $customerAddressStandard->lastname = $request->standard_lastname;
        }
        if (Helper::checkRole('customers', 'standard_firmname') === true) {
            $customerAddressStandard->firmname = $request->standard_firmname;
        }
        if (Helper::checkRole('customers', 'standard_nip') === true) {
            $customerAddressStandard->nip = $request->standard_nip;
        }
        if (Helper::checkRole('customers', 'standard_phone') === true) {
            $customerAddressStandard->phone = $request->standard_phone;
        }
        if (Helper::checkRole('customers', 'standard_address') === true) {
            $customerAddressStandard->address = $request->standard_address;
        }
        if (Helper::checkRole('customers', 'standard_flat_number') === true) {
            $customerAddressStandard->flat_number = $request->standard_flat_number;
        }
        if (Helper::checkRole('customers', 'standard_city') === true) {
            $customerAddressStandard->city = $request->standard_city;
        }
        if (Helper::checkRole('customers', 'standard_postal_code') === true) {
            $customerAddressStandard->postal_code = $request->standard_postal_code;
        }
        if (Helper::checkRole('customers', 'standard_email') === true) {
            $customerAddressStandard->email = $request->standard_email;
        }
        $customerAddressStandard->save();

        /** @var CustomerAddress $customerAddressInvoice */
        $customerAddressInvoice = $customer->invoiceAddress();

        if (Helper::checkRole('customers', 'invoice_firstname') === true) {
            $customerAddressInvoice->firstname = $request->invoice_firstname;
        }
        if (Helper::checkRole('customers', 'invoice_lastname') === true) {
            $customerAddressInvoice->lastname = $request->invoice_lastname;
        }
        if (Helper::checkRole('customers', 'invoice_firmname') === true) {
            $customerAddressInvoice->firmname = $request->invoice_firmname;
        }
        if (Helper::checkRole('customers', 'invoice_nip') === true) {
            $customerAddressInvoice->nip = $request->invoice_nip;
        }
        if (Helper::checkRole('customers', 'invoice_phone') === true) {
            $customerAddressInvoice->phone = $request->invoice_phone;
        }
        if (Helper::checkRole('customers', 'invoice_address') === true) {
            $customerAddressInvoice->address = $request->invoice_address;
        }
        if (Helper::checkRole('customers', 'invoice_flat_number') === true) {
            $customerAddressInvoice->flat_number = $request->invoice_flat_number;
        }
        if (Helper::checkRole('customers', 'invoice_city') === true) {
            $customerAddressInvoice->city = $request->invoice_city;
        }
        if (Helper::checkRole('customers', 'invoice_postal_code') === true) {
            $customerAddressInvoice->postal_code = $request->invoice_postal_code;
        }
        if (Helper::checkRole('customers', 'invoice_email') === true) {
            $customerAddressInvoice->email = $request->invoice_email;
        }
        $customerAddressInvoice->save();


        $customerAddressDelivery = $customer->deliveryAddress();

        if (Helper::checkRole('customers', 'delivery_firstname') === true) {
            $customerAddressDelivery->firstname = $request->delivery_firstname;
        }
        if (Helper::checkRole('customers', 'delivery_lastname') === true) {
            $customerAddressDelivery->lastname = $request->delivery_lastname;
        }
        if (Helper::checkRole('customers', 'delivery_firmname') === true) {
            $customerAddressDelivery->firmname = $request->delivery_firmname;
        }
        if (Helper::checkRole('customers', 'delivery_nip') === true) {
            $customerAddressDelivery->nip = $request->delivery_nip;
        }
        if (Helper::checkRole('customers', 'delivery_phone') === true) {
            $customerAddressDelivery->phone = $request->delivery_phone;
        }
        if (Helper::checkRole('customers', 'delivery_address') === true) {
            $customerAddressDelivery->address = $request->delivery_address;
        }
        if (Helper::checkRole('customers', 'delivery_flat_number') === true) {
            $customerAddressDelivery->flat_number = $request->delivery_flat_number;
        }
        if (Helper::checkRole('customers', 'delivery_city') === true) {
            $customerAddressDelivery->city = $request->delivery_city;
        }
        if (Helper::checkRole('customers', 'delivery_postal_code') === true) {
            $customerAddressDelivery->postal_code = $request->delivery_postal_code;
        }
        if (Helper::checkRole('customers', 'delivery_email') === true) {
            $customerAddressDelivery->email = $request->delivery_email;
        }
        $customerAddressDelivery->save();


        $customer->is_staff = $request->is_staff;
        $customer->save();

        return redirect()->back()->with([
            'message' => __('customers.message.update'),
            'alert-type' => 'success'
        ]);

    }

    /**
     * @return JsonResponse
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
        $query = DB::table('customers')
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
        $query = DB::table('customers')
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
}




