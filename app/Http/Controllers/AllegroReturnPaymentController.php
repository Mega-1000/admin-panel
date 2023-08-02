<?php

namespace App\Http\Controllers;
use App\DTO\AllegroPayment\AllegroReturnDTO;
use App\DTO\AllegroPayment\AllegroReturnItemDTO;
use App\Entities\Order;
use App\Entities\OrderReturn;
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
        $order = Order::with(['items'])->findOrFail($orderId);

        foreach ($order['items'] as $item) {
            $productId = $item->product_id;
            $orderReturn = OrderReturn::with(['product'])->where('product_id', $productId)->where('order_id', $orderId)->first();
            $item['orderReturn'] = $orderReturn;
        }

        $existingAllegroReturns = $this->allegroPaymentService->getRefundsByPaymentId($order['allegro_payment_id']);

        return view('allegro-return.index', [
            'order' => $order,
            'existingAllegroReturns' => $existingAllegroReturns,
        ]);
    }

    public function store(Request $request, int $orderId) {
        $order = Order::with(['items', 'orderReturn'])->findOrFail($orderId);

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
            $quantityUndamaged = $itemReturn['quantityUndamaged'];
            $quantityDamaged = $itemReturn['quantityDamaged'];
            $quantityTotal = $quantityUndamaged + $quantityDamaged;

            if (array_key_exists('deductionCheck', $itemReturn) && strtolower($itemReturn['deductionCheck']) === "on") {
                $amount = $quantityUndamaged * (float)$itemReturn['price'] - (float)$itemReturn['deduction'];
                $lineItems[] = new AllegroReturnItemDTO(
                    id: $allegroId,
                    type: AllegroReturnItemTypeEnum::AMOUNT(),
                    amount: $amount,
                );

                continue;
            }
            
            $lineItems[] = new AllegroReturnItemDTO(
                id: $allegroId,
                type: AllegroReturnItemTypeEnum::QUANTITY(),
                quantity: $quantityUndamaged,
            );
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
