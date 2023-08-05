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
use App\Services\AllegroPaymentsReturnService;
use App\Services\Label\RemoveLabelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Contracts\View\View;

class AllegroReturnPaymentController extends Controller
{
    public function __construct(
        private readonly AllegroPaymentService $allegroPaymentService,
        private readonly AllegroOrderService $allegroOrderService,
        private readonly AllegroPaymentsReturnService $allegroPaymentsReturnService,
    ) {}
    
    public function index(int $orderId): RedirectResponse|View 
    {
        $order = $this->allegroPaymentsReturnService->getOrderWithItems($orderId);

        if (empty($order)) {
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

    public function store(Request $request, int $orderId): RedirectResponse
    {
        $order = Order::with(['items', 'orderReturn'])->findOrFail($orderId);

        $allegroPaymentId = $order['allegro_payment_id'];
        $allegroOrder = $this->allegroOrderService->getOrderByPaymentId($allegroPaymentId);
        
        $returnsByAllegroId = AllegroReturnPaymentHelper::createReturnsByAllegroId($allegroOrder, $request->return);

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

        $unsuccessfulCommissionRefundsItemNames = $this->allegroPaymentsReturnService->returnCommissionsAndReturnFailed($lineItemsForCommissionRefund, $returnsByAllegroId);

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
