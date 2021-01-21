<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\DelivererPackageImport\Builders\DelivererImportRulesBuilder;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use App\Domains\DelivererPackageImport\TransportPaymentImporter;
use App\Entities\Deliverer;
use App\Http\DTOs\DelivererCreateImportRulesDTO;
use App\Http\Requests\DelivererCreateRequest;
use App\Http\Requests\DelivererEditRequest;
use App\Http\Requests\TransportPaymentsImportRequest;
use App\Domains\DelivererPackageImport\Services\DelivererService;
use App\Repositories\DelivererRepositoryEloquent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DelivererController extends Controller
{
    private $delivererService;

    public function __construct(DelivererService $delivererService)
    {
        $this->delivererService = $delivererService;
    }

    public function list(): View
    {
        return view('transport.list', ['deliverers' => Deliverer::all()]);
    }

    public function create(): View
    {
        return view('transport.create', [
            'columns' => DelivererRulesColumnNameEnum::getInstances(),
            'csvColumnsNumbers' => range(1,20),
            'actions' => DelivererRulesActionEnum::getInstances(),
        ]);
    }

    public function store(
        DelivererCreateRequest $request,
        DelivererImportRulesBuilder $delivererImportRulesBuilder
    ): RedirectResponse {
        if ($this->delivererService->getDelivererByName($request->getName())) {
            return redirect()->route('transportPayment.list')->with([
                'message' => __('transport.errors.exists'),
                'alert-type' => 'error',
            ]);
        }

        try {
            $deliverer = $this->delivererService->createDeliverer($request->getName());

            $importRules = $delivererImportRulesBuilder->buildFromRequest(
                $deliverer,
                new DelivererCreateImportRulesDTO($request->getImportRules())
            );

            $this->delivererService->saveDelivererImportRules(
                $deliverer,
                $importRules
            );

            return redirect()->route('transportPayment.list')->with([
                'message' => __('voyager.generic.successfully_added_new'),
                'alert-type' => 'success',
            ]);
        } catch (\Exception $exception) {
            return redirect()->route('transportPayment.list')->with([
                'message' => $exception->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|RedirectResponse|View
     */
    public function edit(int $delivererId)
    {
        if ($deliverer = $this->delivererService->findDeliverer($delivererId)) {
            return view('transport.edit', [
                'deliverer' => $deliverer,
                'columns' => DelivererRulesColumnNameEnum::getInstances(),
                'csvColumnsNumbers' => range(1,20),
                'actions' => DelivererRulesActionEnum::getInstances(),
            ]);
        }

        return redirect()->route('transportPayment.list')->with([
            'message' => __('transport.errors.not-found'),
            'alert-type' => 'error',
        ]);
    }

    public function update(
        DelivererEditRequest $request,
        DelivererImportRulesBuilder $delivererImportRulesBuilder,
        int $delivererId
    ): RedirectResponse {
        $deliverer = $this->delivererService->findDeliverer($delivererId);
        if (!$deliverer) {
            return redirect()->route('transportPayment.list')->with([
                'message' => __('transport.errors.not-found'),
                'alert-type' => 'error',
            ]);
        }

        if ($this->delivererService->updateDeliverer(
            $deliverer,
            $request->getName(),
            $delivererImportRulesBuilder->buildFromRequest(
                $deliverer,
                new DelivererCreateImportRulesDTO($request->getImportRules())
            )
        )) {
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

    public function delete(
        Request $request,
        DelivererRepositoryEloquent $delivererRepository
    ): RedirectResponse {
        try {
            $deliverer = $delivererRepository->findById((int) $request->id);

            $this->delivererService->deleteDeliverer($deliverer);

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
        ini_set('max_execution_time', '10000');

        $deliverer = $this->delivererService->findDeliverer((int) $request->input('delivererId'));

        if (!$deliverer) {
            return redirect()->route('orders.index')->with([
                'message' => __('transport.errors.not-found'),
                'alert-type' => 'error',
            ]);
        }

        try {
            $transportPaymentImporter->import(
                $deliverer,
                $this->delivererService->saveFileToImport($request->file('file'))
            );

            return redirect()->route('orders.index')->with([
                'message' => __('transport.messages.import-finished'),
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('orders.index')->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error'
            ]);
        }
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
