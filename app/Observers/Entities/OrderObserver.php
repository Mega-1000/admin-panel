<?php

namespace App\Observers\Entities;

use App\Entities\BuyingInvoice;
use App\Entities\Order;
use App\Entities\Status;
use App\Facades\Mailer;
use App\Helpers\OrderDepositPaidCalculator;
use App\Helpers\OrderPackagesCalculator;
use App\Helpers\OrdersRecalculatorBasedOnPeriod;
use App\Helpers\RecalculateBuyingLabels;
use App\Jobs\calculateLabelsForOrder;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\FireProductPacketJob;
use App\Mail\ShipmentDateInOrderChangedMail;
use App\Repositories\OrderRepository;
use App\Repositories\StatusRepository;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\LabelService;
use App\Services\OrderPaymentLabelsService;
use App\Services\OrderService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

readonly class OrderObserver
{
    public function __construct(
        protected StatusRepository             $statusRepository,
        protected OrderPaymentLabelsService    $orderPaymentLabelsService,
        protected OrderService                 $orderService,
        protected OrderPackagesCalculator      $orderPackagesCalculator,
        protected OrderDepositPaidCalculator   $orderDepositPaidCalculator,
        protected LabelService                 $labelService,
        protected OrderRepository              $orderRepository,
    ) {}

    /**
     * @throws Exception
     */
    public function created(Order $order): void
    {
        $order->employee_id = 12;
        $order->save();

        $relatedPaymentsValue = round($this->orderRepository->getAllRelatedOrderPaymentsValue($order), 2);
        $relatedOrdersValue = round($this->orderRepository->getAllRelatedOrdersValue($order), 2);
        $orderReturnGoods = round($this->orderRepository->getOrderReturnGoods($order), 2);

        $arr = [];

        $relatedPaymentsValue -= $orderReturnGoods;

        if (count($this->orderRepository->getAllRelatedOrderPayments($order)) === 0) {
            $this->labelService->removeLabel($order->id, [134]);
            return;
        }

        if (round($relatedOrdersValue, 2) === round($relatedPaymentsValue, 2)) {
            $this->labelService->removeLabel($order->id, [134]);
            AddLabelService::addLabels($order, [133], $arr, [], Auth::user()?->id);

            return;
        }
        $this->labelService->removeLabel($order->id, [133]);
        AddLabelService::addLabels($order, [134], $arr, [], Auth::user()?->id);


        $labels = $order->labels()->get()->pluck('id')->toArray();
        $order->token = Str::random(32);
        $order->save();

        dispatch(new FireProductPacketJob($order));
        dispatch(new calculateLabelsForOrder($order));
    }

    public function updating(Order $order): void
    {
        if (!$order->isDirty()) {
            return;
        }

        if (!empty($order->getDirty()['status_id'])) {
            $statusId = $order->getDirty()['status_id'];
            /** @var Status $status */
            $status = Status::query()->find($statusId);
            $loopPresentationArray = [];
            AddLabelService::addLabels($order, $status->labelsToAddOnChange()->pluck('labels.id')->toArray(), $loopPresentationArray, [], Auth::user()?->id);
        }

        if (!empty($order->getDirty()['employee_id'])) {
            dispatch(new DispatchLabelEventByNameJob($order, "consultant-changed"));
        }

        if (!empty($order->getDirty()['shipment_date'])) {
            $original = $order->getOriginal('shipment_date');
            $newDate = $order->shipment_date;

            if ((new Carbon($original))->diffInDays($newDate) !== 0) {
                try {
                    if (strpos($order->customer->login, 'allegromail.pl')) {
                        return;
                    }
                    Mailer::create()
                        ->to($order->customer->login)
                        ->send(new ShipmentDateInOrderChangedMail([
                            'oldDate' => $original,
                            'newDate' => $newDate,
                            'orderId' => $order->id,
                        ]));
                } catch (Exception $exception) {
                    Log::error('Can\'t send email about shipment date change .',
                        ['exception' => $exception->getMessage(), 'class' => $exception->getFile(), 'line' => $exception->getLine()]
                    );
                }
            }
        }
    }

    public function updated(Order $order): void
    {
        RecalculateBuyingLabels::recalculate($order);
    }
}
