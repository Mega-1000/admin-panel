<?php

namespace App\Http\Controllers;
use App\Entities\Order;
use App\Helpers\AllegroOrderHelper;
use App\Services\AllegroOrderService;
use App\Services\AllegroPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AllegroReturnPaymentController extends Controller
{
    
    public function __construct(
        private readonly AllegroPaymentService $allegroPaymentService,
        private readonly AllegroOrderService $allegroOrderService,
     ) {}
    
    public function index(int $orderId) {
        $order = Order::with(['items', 'orderReturn'])->find($orderId);

        if (empty($order)) {
            abort(404);
        }

        $existingAllegroReturns = $this->allegroPaymentService->getRefundsByPaymentId($order['allegro_payment_id']);

        return view('allegro-return.index', [
            'order' => $order,
            'existingAllegroReturns' => $existingAllegroReturns,
        ]);
    }

    public function store(Request $request, int $orderId) {
        $order = Order::with(['items', 'orderReturn'])->find($orderId);

        if (empty($order)) {
            abort(404);
        }

        $allegroPaymentId = $order['allegro_payment_id'];
        $allegroOrder = $this->allegroOrderService->getOrderByPaymentId($allegroPaymentId);
        $symbolToAllegroIdPairings = AllegroOrderHelper::createSymbolToAllegroIdPairingsFromLineItems($allegroOrder['lineItems']);

        $returnsByAllegroId = [];

        foreach ($request->return as $symbol => $itemReturn) {
            $symbol = explode("-", $symbol)[0];
            $returnsByAllegroId[$symbolToAllegroIdPairings[$symbol]] = $itemReturn;
        }

        $lineItems = [];

        foreach ($returnsByAllegroId as $allegroId => $itemReturn) {
            if (!array_key_exists('check', $itemReturn) || strtolower($itemReturn['check']) !== "on" || !array_key_exists('quantity', $itemReturn) || $itemReturn['quantity'] <= 0) {
                continue;
            }

            if (array_key_exists('deductionCheck', $itemReturn) && strtolower($itemReturn['deductionCheck']) === "on") {
                $amount = (int)$itemReturn['quantity'] * (float)$itemReturn['price'] - (float)$itemReturn['deduction'];
                $lineItems[] = [
                    'id' => $allegroId,
                    'type' => 'AMOUNT',
                    'value' => [
                        'amount' => $amount ,
                        'currency' => 'PLN',
                    ],
                ];
            } else {
                $lineItems[] = [
                    'id' => $allegroId,
                    'type' => 'QUANTITY',
                    'quantity' => $itemReturn['quantity'],
                ];
            }
        }

        dd($lineItems);

        return redirect()->route('allegro-return.index', ['orderId' => $orderId]);
    }
}
