<?php

namespace App\Http\Controllers;
use App\DTO\AllegroPayment\AllegroReturnDTO;
use App\DTO\AllegroPayment\AllegroReturnItemDTO;
use App\Entities\Order;
use App\Enums\AllegroReturnItemTypeEnum;
use App\Helpers\AllegroOrderHelper;
use App\Services\AllegroOrderService;
use App\Services\AllegroPaymentService;
use Illuminate\Http\Request;

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
            return response(status: 404);
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

            $quantity = (int)$itemReturn['quantity'];

            if (array_key_exists('deductionCheck', $itemReturn) && strtolower($itemReturn['deductionCheck']) === "on") {
                $amount = $quantity * (float)$itemReturn['price'] - (float)$itemReturn['deduction'];
                $lineItems[] = new AllegroReturnItemDTO(
                    id: $allegroId,
                    type: AllegroReturnItemTypeEnum::fromValue(AllegroReturnItemTypeEnum::AMOUNT),
                    amount: $amount,
                );
            } else {
                $lineItems[] = new AllegroReturnItemDTO(
                    id: $allegroId,
                    type: AllegroReturnItemTypeEnum::fromValue(AllegroReturnItemTypeEnum::QUANTITY),
                    quantity: $quantity,
                );
            }
        }

        $data = new AllegroReturnDTO(
            paymentId: $allegroPaymentId,
            reason: $request->reason,
            lineItems: $lineItems,
        );

        $this->allegroPaymentService->initiateRefund($data);

        return redirect()->route('allegro-return.index', ['orderId' => $orderId]);
    }
}
