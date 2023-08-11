<?php

namespace App\Http\Controllers;

use App\Entities\LabelGroup;
use App\Entities\Task;
use App\Helpers\AllegroReturnPaymentHelper;
use App\DTO\AllegroPayment\AllegroReturnDTO;
use App\Entities\Label;
use App\Entities\Order;
use App\Helpers\MessagesHelper;
use App\Services\AllegroOrderService;
use App\Services\AllegroPaymentService;
use App\Services\AllegroPaymentsReturnService;
use App\Services\Label\RemoveLabelService;
use Carbon\Carbon;
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
        if (empty($order->allegro_payment_id)) {
            return redirect()->route('orders.index')->with([
                'message' => 'Ta oferta nie jest ofertą Allegro',
                'alert-type' => 'error',
            ]);
        }

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
        $allegroPaymentId = $order->allegro_payment_id;
        $allegroOrder = $this->allegroOrderService->getOrderByPaymentId($allegroPaymentId);
        
        $returnsByAllegroId = AllegroReturnPaymentHelper::createReturnsByAllegroId($allegroOrder, $request->returns);

        list($lineItemsForPaymentRefund, $lineItemsForCommissionRefund) = AllegroReturnPaymentHelper::createLineItemsFromReturnsByAllegroId($returnsByAllegroId);

        dd($lineItemsForPaymentRefund, $lineItemsForCommissionRefund);

        $totalValue = null;

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

            $totalValue = $response['totalValue']['amount'];

            $consultantNotice = $response['createdAt'] . " Zwrot płatności: " . $response['id'] . " o wartości " . $response['totalValue']['amount'];
            MessagesHelper::sendAsCurrentUser($order, $consultantNotice);

            $order->update([
                'return_payment_id' => $response['id'],
            ]);
        }
        
        $loopPreventionArray = [];
        RemoveLabelService::removeLabels($order, [Label::NEED_TO_RETURN_PAYMENT], $loopPreventionArray, [], Auth::user()?->id);
        $order->labels()->attach(Label::NEED_TO_ISSUE_INVOICE_CORRECTION);

        if (!$order->isConstructed()) {
            $loopPreventionArray = [];
            $transportLabels = LabelGroup::query()->find(LabelGroup::TRANSPORT_LABEL_GROUP_ID)->labels()->pluck('labels.id')->toArray();
            $toRemove = [Label::BLUE_HAMMER_ID, Label::RED_HAMMER_ID, Label::ORDER_ITEMS_UNDER_CONSTRUCTION];
            $toRemove = array_merge($toRemove, $transportLabels);
            $order->labels()->detach($toRemove);

            $order->taskSchedule()->whereNotIn('status', [Task::FINISHED, Task::REJECTED])->delete();

            $order->cancelAllPackages();

            if (isset($totalValue)) {
                $order->payments()->create([
                    'promise' => '1',
                    'promise_date' => Carbon::now()->addWeekdays(2)->toDateTimeString(),
                    'declared_sum' => "-$totalValue",
                    'type' => 'CLIENT'
                ]);
            }
        }

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
