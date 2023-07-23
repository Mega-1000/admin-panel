<?php

namespace App\Http\Controllers;
use App\Entities\Order;
use App\Services\AllegroPaymentService;

class AllegroReturnPaymentController extends Controller
{
    
    public function __construct(private readonly AllegroPaymentService $allegroPaymentService) {}
    
    public function index(int $orderId) {
        $order = Order::with(['items', 'orderReturn'])->find($orderId);

        if (empty($order)) {
            abort(404);
        }

        $existingAllegroReturns = $this->allegroPaymentService->getNotCancelledReturnsByPaymentId($order['allegro_payment_id']);

        return view('allegro-return.index', [
            'order' => $order,
            'existingAllegroReturns' => $existingAllegroReturns,
        ]);
    }

    public function store(int $orderId) {
        $order = Order::with(['items', 'orderReturn'])->find($orderId);

        if (empty($order)) {
            abort(404);
        }

        return redirect()->route('allegro-return.index', ['orderId' => $orderId]);
    }
}
