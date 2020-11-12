<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Builders\DelivererImportRulesBuilder;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Entities\Deliverer;
use App\Helpers\transportPayments\TransportPaymentImporter;
use App\Http\DTOs\DelivererCreateImportRulesDTO;
use App\Http\Requests\DelivererCreateRequest;
use App\Http\Requests\DelivererEditRequest;
use App\Services\TransportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TransportPaymentsController extends Controller
{
    private $transportService;

    public function __construct(TransportService $transportService)
    {
        $this->transportService = $transportService;
    }

    public function list(): View
    {
        return view('transport.list', ['deliverers' => Deliverer::all()]);
    }

    public function create(): View
    {
        return view('transport.create', [
            'columns' => DelivererRulesColumnNameEnum::getValues(),
            'csvColumnsNumbers' => range(1,20),
            'actions' => DelivererRulesActionEnum::getInstances(),
        ]);
    }

    public function store(
        DelivererCreateRequest $request,
        DelivererImportRulesBuilder $delivererImportRulesBuilder
    ): RedirectResponse {
        /*if ($this->transportService->getDelivererByName($request->getName())) {
            return redirect()->route('transportPayment.list')->with([
                'message' => __('transport.errors.exists'),
                'alert-type' => 'error',
            ]);
        }*/

        if (!$deliverer = $this->transportService->getDelivererByName($request->getName())) {
            $deliverer = $this->transportService->createDeliverer($request->getName());
        }

        $importRules = $delivererImportRulesBuilder->buildFromRequest(
            $deliverer,
            new DelivererCreateImportRulesDTO($request->getImportRules())
        );

        $this->transportService->saveDelivererImportRules(
            $deliverer,
            $importRules
        );

        dd('OK');

        /*try {
            $this->transportService->createDeliverer($request->getName());
            $this->transportService->createDelivererImportRules();

            return redirect()->route('transportPayment.list')->with([
                'message' => __('voyager.generic.successfully_added_new'),
                'alert-type' => 'success',
            ]);
        } catch (\Exception $exception) {
            return redirect()->route('transportPayment.list')->with([
                'message' => $exception->getMessage(),
                'alert-type' => 'error',
            ]);
        }*/
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|RedirectResponse|View
     */
    public function edit(int $delivererId)
    {
        $deliverer = $this->transportService->getDeliverer($delivererId);
        if ($deliverer) {
            return view('transport.edit', ['deliverer' => $deliverer]);
        }

        return redirect()->route('transportPayment.list')->with([
            'message' => __('transport.errors.not-found'),
            'alert-type' => 'error',
        ]);
    }

    public function update(DelivererEditRequest $request, int $delivererId): RedirectResponse
    {
        $deliverer = $this->transportService->getDeliverer($delivererId);
        if (!$deliverer) {
            return redirect()->route('transportPayment.list')->with([
                'message' => __('transport.errors.not-found'),
                'alert-type' => 'error',
            ]);
        }

        if ($this->transportService->updateDeliverer($deliverer, $request->getName())) {
            return redirect()->route('transportPayment.list')->with([
                'message' => __('voyager.generic.successfully_updated'),
                'alert-type' => 'success',
            ]);
        }

        return redirect()->route('transportPayment.list')->with([
            'message' => __('voyager.generic.update_failed'),
            'alert-type' => 'error',
        ]);
    }







    public function delete(Request $request)
    {
        try {
            $deliverer = Deliverer::findOrFail($request->id);
            $deliverer->delete();
            return redirect()->route('transportPayment.list')->with([
                'message' => __('voyager.generic.successfully_deleted'),
                'alert-type' => 'success'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('transportPayment.list')->with([
                'message' => __('transport.errors.not-found'),
                'alert-type' => 'error'
            ]);
        }
    }

    public function updatePricing(Request $request)
    {
        $file = $request->file('file');
        $maxFileSize = 20000000;
        if ($file->getSize() > $maxFileSize) {
            return redirect()->route('orders.index')->with([
                'message' => __('transport.errors.too-big-file'),
                'alert-type' => 'error'
            ]);
        }

        do {
            $fileName = Str::random(40) . '.csv';
            $path = Storage::path('user-files/transport/') . $fileName;
        } while (file_exists($path));

        $file->move(Storage::path('user-files/transport/'), $fileName);

        try {
            $deliverer = Deliverer::findOrFail($request)->first();
            $importer = new TransportPaymentImporter();
            $importer->setColumnNetPayment($deliverer->net_payment_column_number)
                ->setColumnGrossPayment($deliverer->gross_payment_column_number_gross)
                ->setColumnLetter($deliverer->letter_number_column_number);
            $errors = $importer->import($fileName);
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
        return redirect()->route('orders.index')->with(
            'update_errors', $errors
        );
    }

    public function store2(Request $request)
    {
        if ($request->gross_payment_column_number_gross) {
            $request->validate([
                'name' => 'required',
                'gross_payment_column_number_gross' => 'numeric',
                'letter_number_column_number' => 'numeric',
            ]);
        } else if ($request->net_payment_column_number) {
            $request->validate([
                'name' => 'required|max:255',
                'net_payment_column_number' => 'numeric',
                'letter_number_column_number' => 'numeric',
            ]);
        } else {
            $request->validate([
                'name' => 'required',
                'net_payment_column_number' => 'numeric',
                'gross_payment_column_number_gross' => 'numeric',
                'letter_number_column_number' => 'numeric',
            ]);
        }
        $deliverer = Deliverer::find($request->id);
        if ($deliverer) {
            $deliverer->update($request->all());
            return redirect()->route('transportPayment.list')->with([
                'message' => __('voyager.generic.successfully_updated'),
                'alert-type' => 'success'
            ]);
        } else {
            Deliverer::create($request->all());
            return redirect()->route('transportPayment.list')->with([
                'message' => __('voyager.generic.successfully_added_new'),
                'alert-type' => 'success'
            ]);
        }
    }
}
