<?php

namespace App\Http\Controllers;

use App\Helpers\AllegroReturnPaymentHelper;
use App\DTO\AllegroPayment\AllegroReturnDTO;
use App\Entities\Label;
use App\Entities\Order;
use App\Helpers\MessagesHelper;
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
    
    public function index(Order $order): RedirectResponse|View 
    {
        $order = $this->allegroPaymentsReturnService->getOrderItemsWithReturns($order);

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

    public function store(Request $request, Order $order): RedirectResponse
    {
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

            $response = $this->allegroPaymentService->initiatePaymentRefund($data);
            if (!$response) {
                return redirect()->route('allegro-return.index', ['order' => $order])->with([
                    'message' => 'Nie udało się zwrócić płatności',
                    'alert-type' => 'error',
                ]);
            }

            $consultantNotice = $response['createdAt'] . "Zwrot płatności: " . $response['id'] . "o wartości" . $response['totalValue']['amount'];
            MessagesHelper::sendAsCurrentUser($order, $consultantNotice);
        }
            
        $loopPreventionArray = [];
        RemoveLabelService::removeLabels($order, [Label::NEED_TO_RETURN_PAYMENT], $loopPreventionArray, [], Auth::user()?->id);

        $unsuccessfulCommissionRefundsItemNames = $this->allegroPaymentsReturnService->returnCommissionsAndReturnFailed($lineItemsForCommissionRefund, $returnsByAllegroId);

        if (count($unsuccessfulCommissionRefundsItemNames) > 0) {
            $message = "Zwrot płatności pomyślny! Nie udało się zwrócić prowizji dla następujących przedmiotów: " . implode(", ", $unsuccessfulCommissionRefundsItemNames);
            return redirect()->route('allegro-return.index', ['order' => $order])->with([
                'message' => $message
            ]);
        }

        return redirect()->route('orders.index')->with([
            'message' => 'Zwrot płatności pomyślny!',
            'alert-type' => 'success',
        ]);
    }
}
