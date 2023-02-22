<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\WorkingEvents;
use App\Jobs\WarehouseStocks\ChangeWarehouseStockJob;
use App\Repositories\LabelRepository;
use App\Repositories\OrderRepository;
use App\Services\OrderWarehouseNotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RemoveLabelJob extends Job implements ShouldQueue
{
    protected $order;
    protected $labelIdsToRemove;
    protected $loopPreventionArray;
    protected $customLabelIdsToAddAfterRemoval = [];
    protected $time;

    /**
     * RemoveLabelJob constructor.
     * @param $order
     * @param $labelIdsToRemove
     * @param $loopPreventionArray
     * @param $customLabelIdsToAddAfterRemoval
     * @param $time
     */
    public function __construct($order, $labelIdsToRemove, &$loopPreventionArray = [], $customLabelIdsToAddAfterRemoval = [], $time = null)
    {
        $this->queue = 'labels';
        $this->order = $order;
        $this->labelIdsToRemove = $labelIdsToRemove;
        $this->loopPreventionArray = $loopPreventionArray;
        $this->time = $time;
        $this->customLabelIdsToAddAfterRemoval = is_array($customLabelIdsToAddAfterRemoval)
            ? $customLabelIdsToAddAfterRemoval
            : [$customLabelIdsToAddAfterRemoval];
    }

    public function handle(
        OrderRepository                   $orderRepository,
        LabelRepository                   $labelRepository,
        OrderWarehouseNotificationService $orderWarehouseNotificationService
    )
    {
        if (!($this->order instanceof Order)) {
            $this->order = $orderRepository->find($this->order);
        }
        WorkingEvents::createEvent(WorkingEvents::LABEL_REMOVE_EVENT, $this->order->id);

        if (count($this->labelIdsToRemove) < 1) {
            return null;
        }

        foreach ($this->labelIdsToRemove as $labelId) {
            if (!empty($this->loopPreventionArray['already-removed']) && in_array($labelId, $this->loopPreventionArray['already-removed'])) {
                continue;
            }

            if ($labelId == 49 && Auth::user()?->role_id == 4) {
                continue;
            }

//            if ($labelId == 41 && !strpos($this->order->customer->login, 'allegromail.pl')) {
//                $noData = DB::table('gt_invoices')->where('order_id', $this->order->id)->where('gt_invoice_status_id', '13')->first();
//                if (!empty($noData)) {
//                    try {
//                        \Mailer::create()
//                            ->to($this->order->customer->login)
//                            ->send(new DifferentCustomerData('Wybór danych do wystawienia faktury - zlecenie ' . $this->order->id, $this->order->id, $noData->id));
//                    } catch (\Swift_TransportException $e) {
//
//                    }
//                } else {
//                    try {
//                        \Mailer::create()
//                            ->to($this->order->customer->login)
//                            ->send(new ConfirmData('Wybór danych do wystawienia faktury  - zlecenie ' . $this->order->id, $this->order->id));
//                    } catch (\Swift_TransportException $e) {
//
//                    }
//                }
//            }

            if ($labelId == Label::PACKAGE_NOTIFICATION_SENT_LABEL) {
                $orderWarehouseNotificationService->removeNotifications($this->order->id);
            }

            $label = $labelRepository->find($labelId);

            if ($this->time !== null) {
                $this->timedLabelChange($label);
            }

            if ($label->manual_label_selection_to_add_after_removal) {
                $labelIdsToAttach = $this->customLabelIdsToAddAfterRemoval;
            } else {
                $labelIdsToAttach = [];
                foreach ($label->labelsToAddAfterRemoval as $item) {
                    $labelIdsToAttach[] = $item->id;
                    if ($item->id == 50) {
                        $response = dispatch(new ChangeWarehouseStockJob($this->order));
                        if (strlen((string)$response) > 0) {
                            Session::put('removeLabelJobAfterProductStockMove', array_merge([$this], Session::get('removeLabelJobAfterProductStockMove') ?? []));
                            return $response;
                        } else {
                            $this->order->labels()->detach($label);
                            $this->loopPreventionArray['already-removed'][] = $labelId;

                            //attaching labels to add after removal
                            if (count($labelIdsToAttach) > 0) {
                                dispatch(new AddLabelJob($this->order, $labelIdsToAttach, $this->loopPreventionArray));
                            }

                            //detaching labels to remove after removal
                            if (count($label->labelsToRemoveAfterRemoval) > 0) {
                                $labelIdsToDetach = [];
                                foreach ($label->labelsToRemoveAfterRemoval as $itemAfterRemoval) {
                                    $labelIdsToDetach[] = $itemAfterRemoval->id;
                                }
                                dispatch(new RemoveLabelJob($this->order, $labelIdsToDetach, $this->loopPreventionArray));
                            }
                        }
                    }
                }
            }

            $this->order->labels()->detach($label);
            $this->loopPreventionArray['already-removed'][] = $labelId;

            //attaching labels to add after removal
            if (count($labelIdsToAttach) > 0) {
                dispatch(new AddLabelJob($this->order, $labelIdsToAttach, $this->loopPreventionArray));
            }

            //detaching labels to remove after removal
            if (count($label->labelsToRemoveAfterRemoval) > 0) {
                $labelIdsToDetach = [];
                foreach ($label->labelsToRemoveAfterRemoval as $item) {
                    $labelIdsToDetach[] = $item->id;
                }
                dispatch(new RemoveLabelJob($this->order, $labelIdsToDetach, $this->loopPreventionArray));
            }
        }
        return null;
    }

    private function timedLabelChange($label)
    {
        $labelsToChange = $label->labelsToAddAfterTimedLabel;
        $now = Carbon::now();
        $targetDatetime = Carbon::parse($this->time);
        $delay = $now->diffInSeconds($targetDatetime);
        foreach ($labelsToChange as $labelToChange) {
            dispatch(new AddLabelJob($this->order->id, [$labelToChange->pivot->label_to_add_id]));
            dispatch(new RemoveLabelJob($this->order->id, [$labelToChange->pivot->label_to_add_id]))->delay($delay);
            dispatch(new AddLabelJob($this->order->id, [$labelToChange->pivot->main_label_id]))->delay($delay);
        }
    }
}
