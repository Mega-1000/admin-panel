<?php

namespace App\Http\Controllers;

use App\Entities\FastResponse;
use App\Entities\Order;
use App\Http\Requests\CreateFastResponseRequest;
use App\Services\FastResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FastResponseController extends Controller
{
    public function __construct(
      protected readonly FastResponseService $fastResponseService,
    ) {}

    public function index(): View
    {
        return view('fast-response.index', [
            'fastResponses' => FastResponse::paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('fast-response.create');
    }

    public function store(CreateFastResponseRequest $request): RedirectResponse
    {
        FastResponse::create($request->validated());

        return redirect()->route('fast-response.index');
    }

    public function destroy(FastResponse $fastResponse): RedirectResponse
    {
        $fastResponse->delete();

        return redirect()->route('fast-response.index');
    }

    public function edit(FastResponse $fastResponse): View
    {
        return view('fast-response.edit', [
            'fastResponse' => $fastResponse,
        ]);
    }

    public function update(CreateFastResponseRequest $request, FastResponse $fastResponse): RedirectResponse
    {
        $fastResponse->update($request->validated());

        return redirect()->route('fast-response.index');
    }

    public function send(FastResponse $fastResponse, Order $order): JsonResponse
    {
        $this->fastResponseService->send($fastResponse, $order);

        return response()->json([
            'success' => true,
        ]);
    }

    public function jsonIndex(): JsonResponse
    {
        return response()->json([
            'fastResponses' => FastResponse::all(),
        ]);
    }
}
