<?php

namespace App\Http\Controllers;

use App\Entities\Task;
use App\Helpers\AllegroReturnPaymentHelper;
use App\DTO\AllegroPayment\AllegroReturnDTO;
use App\Entities\Order;
use App\Helpers\MessagesHelper;
use App\Repositories\Orders;
use App\Services\AllegroOrderService;
use App\Services\AllegroPaymentService;
use App\Services\AllegroPaymentsReturnService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Illuminate\Contracts\View\View;

class AllegroReturnPaymentController extends Controller
{
    public function __construct(
        private readonly AllegroPaymentService $allegroPaymentService,
        private readonly AllegroOrderService $allegroOrderService,
        private readonly AllegroPaymentsReturnService $allegroPaymentsReturnService,
        private readonly Orders $orderRepository,
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

        if (count($lineItemsForPaymentRefund) > 0) {
            $data = new AllegroReturnDTO(
                paymentId: $allegroPaymentId,
                reason: $request->reason,
                lineItems: $lineItemsForPaymentRefund,
                addShipmentPrice: $request->addShipmentPrice ?? true,
                order: $order,
            );

            $response = $this->allegroPaymentService->initiatePaymentRefund($data);
            if (!$response) {
                return redirect()->route('allegro-return.index', ['order' => $order])->with([
                    'message' => 'Nie udało się zwrócić płatności',
                    'alert-type' => 'error',
                ]);
            }

            $totalValue = $response['totalValue']['amount'];

            $consultantNotice = $response['createdAt'] . " Zwrot płatności: " . $response['id'] . " o wartości " . $totalValue;
            MessagesHelper::sendAsCurrentUser($order, $consultantNotice);

            $order->update([
                'return_payment_id' => $response['id'],
            ]);
        }

        $this->allegroPaymentsReturnService->removeAndAddNeccessaryLabelsAfterAllegroReturn($order);

        if (!$this->orderRepository->orderIsConstructed($order)) {
            $order->taskSchedule()->whereNotIn('status', [Task::FINISHED, Task::REJECTED])->delete();

            $this->orderRepository->deleteNewOrderPackagesAndCancelOthers($order);

            if (isset($totalValue)) {
                $declaredSum = -(float)$totalValue;

                $order->payments()->create([
                    'promise' => '1',
                    'promise_date' => Carbon::now()->addWeekdays(2)->toDateTimeString(),
                    'declared_sum' => $declaredSum,
                    'type' => 'CLIENT'
                ]);
            }
        }

        $unsuccessfulCommissionRefundsItemNames = $this->allegroPaymentsReturnService->returnCommissionsAndReturnFailed($lineItemsForCommissionRefund, $returnsByAllegroId);

        if (count($unsuccessfulCommissionRefundsItemNames) > 0) {
            $message = "Zwrot płatności pomyślny! Nie udało się zwrócić prowizji dla następujących przedmiotów: " . implode(", ", $unsuccessfulCommissionRefundsItemNames);
            return redirect()->back()->with([
                'message' => $message
            ]);
        }

        return redirect()->route('orders.index')->with([
            'message' => 'Zwrot płatności pomyślny!',
            'alert-type' => 'success',
        ]);
    }
}
