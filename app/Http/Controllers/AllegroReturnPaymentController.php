<?php

namespace App\Http\Controllers;

use App\Helpers\AllegroReturnPaymentHelper;
use App\DTO\AllegroPayment\AllegroReturnDTO;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderReturn;
use App\Helpers\AllegroOrderHelper;
use App\Services\AllegroOrderService;
use App\Services\AllegroPaymentService;
use App\Services\Label\RemoveLabelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AllegroReturnPaymentController extends Controller
{
    public function __construct(
        private readonly AllegroPaymentService $allegroPaymentService,
        private readonly AllegroOrderService $allegroOrderService,
    ) {}
    
    public function index(int $orderId) 
    {
        $order = Order::with(['items'])->findOrFail($orderId);

        $hasOrderReturn = false;

        foreach ($order['items'] as $item) {
            $productId = $item->product_id;
            $orderReturn = OrderReturn::with(['product'])->where('product_id', $productId)->where('order_id', $orderId)->orderByDesc('created_at')->first();
            if (empty($orderReturn)) {
                $item['orderReturn'] = null;
                continue;
            }
            $item['orderReturn'] = $orderReturn;
            $hasOrderReturn = true;
        }

        if (!$hasOrderReturn) {
            return redirect()->route('orders.index')->with([
                'message' => 'Nie można zwrócić płatności, ponieważ nie ma zwrotów dla tego zamówienia',
                'alert-type' => 'error',
            ]);
        }

        $existingAllegroReturns = $this->allegroPaymentService->getRefundsByPaymentId($order['allegro_payment_id']);

        return view('allegro-return.index', [
            'order' => $order,
            'existingAllegroReturns' => $existingAllegroReturns,
        ]);
    }

    public function store(Request $request, int $orderId) 
    {
        $order = Order::with(['items', 'orderReturn'])->findOrFail($orderId);

        $allegroPaymentId = $order['allegro_payment_id'];
        $allegroOrder = $this->allegroOrderService->getOrderByPaymentId($allegroPaymentId);
        $symbolToAllegroIdPairings = AllegroOrderHelper::createSymbolToAllegroIdPairingsFromLineItems($allegroOrder['lineItems']);

        $returnsByAllegroId = [];

        foreach ($request->return as $symbol => $itemReturn) {
            $symbol = explode("-", $symbol)[0];
            $returnsByAllegroId[$symbolToAllegroIdPairings[$symbol]] = $itemReturn;
        }

        list($lineItemsForPaymentRefund, $lineItemsForCommissionRefund) = AllegroReturnPaymentHelper::createLineItemsFromReturnsByAllegroId($returnsByAllegroId);

        if (count($lineItemsForPaymentRefund) > 0) {
            $data = new AllegroReturnDTO(
                paymentId: $allegroPaymentId,
                reason: $request->reason,
                lineItems: $lineItemsForPaymentRefund,
            );

            $refundCreatedSuccessfully = $this->allegroPaymentService->initiatePaymentRefund($data);
            if (!$refundCreatedSuccessfully) {
                return redirect()->route('allegro-return.index', ['orderId' => $orderId])->with([
                    'message' => 'Nie udało się zwrócić płatności',
                    'alert-type' => 'error',
                ]);
            }
        }
            
        $loopPreventionArray = [];
        RemoveLabelService::removeLabels($order, [Label::NEED_TO_RETURN_PAYMENT], $loopPreventionArray, [], Auth::user()?->id);

        $unsuccessfulCommissionRefundsItemNames = [];

        foreach ($lineItemsForCommissionRefund as $lineItem) {
            $commissionRefundCreatedSuccessfully = $this->allegroPaymentService->createCommissionRefund($lineItem['id'], $lineItem['quantity']);
            if (!$commissionRefundCreatedSuccessfully) {
                $itemName = $returnsByAllegroId[$lineItem['id']]['name'];
                $unsuccessfulCommissionRefundsItemNames[] = $itemName;
            }
        }

        if (count($unsuccessfulCommissionRefundsItemNames) > 0) {
            $message = "Zwrot płatności pomyślny! Nie udało się zwrócić prowizji dla następujących przedmiotów: " . implode(", ", $unsuccessfulCommissionRefundsItemNames);
            return redirect()->route('allegro-return.index', ['orderId' => $orderId])->with([
                'message' => $message
            ]);
        }

        return redirect()->route('orders.index')->with([
            'message' => 'Zwrot płatności pomyślny!',
            'alert-type' => 'success',
        ]);
    }
}
