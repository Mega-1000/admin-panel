<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\DelivererPackageImport\Builders\DelivererImportRulesBuilder;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Domains\DelivererPackageImport\ImportRules\DelivererImportRulesManager;
use App\Domains\DelivererPackageImport\Repositories\DelivererImportRuleRepositoryEloquent;
use App\Domains\DelivererPackageImport\TransportPaymentImporter;
use App\Entities\Deliverer;
use App\Http\DTOs\DelivererCreateImportRulesDTO;
use App\Http\Requests\DelivererCreateRequest;
use App\Http\Requests\DelivererEditRequest;
use App\Http\Requests\TransportPaymentsImportRequest;
use App\Services\TransportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Nexmo\Client\Exception\Transport;

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

        return redirect()->route('transportPayment.list')->with([
            'message' => __('voyager.generic.successfully_added_new'),
            'alert-type' => 'success',
        ]);

        //dd('OK');

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

    public function updatePricing(
        TransportPaymentsImportRequest $request,
        TransportPaymentImporter $transportPaymentImporter
    ): RedirectResponse {
        $deliverer = $this->transportService->getDeliverer((int) $request->input('delivererId'));

        if (!$deliverer) {
            return redirect()->route('orders.index')->with([
                'message' => __('transport.errors.not-found'),
                'alert-type' => 'error',
            ]);
        }

        try {
            $transportPaymentImporter->import(
                $deliverer,
                $this->transportService->saveFileToImport($request->file('file'))
            );

            return redirect()->route('transportPayment.list')->with([
                'message' => __('transport.messages.import-finished'),
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
        /*return redirect()->route('orders.index')->with(
            'update_errors', $errors
        );*/
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
