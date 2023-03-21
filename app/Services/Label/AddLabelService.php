<?php

namespace App\Services\Label;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderLabelScheduler;
use App\Entities\OrderLabelSchedulerAwait;
use App\Entities\ShipmentGroup;
use App\Entities\Task;
use App\Entities\WorkingEvents;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Jobs\TimedLabelJob;

class AddLabelService
{

    public static function addLabels(Order $order, array $labelIdsToAdd, array &$loopPreventionArray, array $options, ?int $userId, ?Carbon $time = null): void
    {
        $now = Carbon::now();
        $labelsToAddAtTheEnd = [];
        $labelsToRemoveAtTheEnd = [];

        $options = array_merge([
            'added_type' => null,
        ], $options);

        WorkingEvents::createEvent(WorkingEvents::LABEL_ADD_EVENT, $order->id);
        if (count($labelIdsToAdd) < 1) {
            return;
        }

        foreach ($labelIdsToAdd as $labelId) {
            $arrayIntersect = array_intersect($order->labels()->pluck('labels.id')->toArray(), Label::NOT_ADD_LABEL_CHECK_CORRECT);
            if ($labelId === 45 && count($arrayIntersect) > 0) {
                continue;
            }

            if (array_key_exists('already-added', $loopPreventionArray)
                && in_array($labelId, $loopPreventionArray['already-added'])) {
                continue;
            }

            /** @var Label $label */
            $label = Label::query()->find($labelId);
            $alreadyHasLabel = $order->labels()->where('label_id', $labelId)->exists();

            // init timed labels
            if ($time !== null && $alreadyHasLabel === false) {
                $timedLabelId = DB::table('timed_labels')->insertGetId([
                    'execution_time' => $time,
                    'order_id' => $order->id,
                    'label_id' => $labelId,
                    'is_executed' => false
                ]);
                $order->labels()->attach($order->id, ['label_id' => $labelId, 'added_type' => $options['added_type'], 'created_at' => Carbon::now()]);

                // calc time to run timed label job
                $dateFrom = Carbon::now();
                $dateTo = new Carbon($time);
                $diff = $dateFrom->diffInSeconds($dateTo);
                
                TimedLabelJob::dispatch($timedLabelId, $labelId, $order, $loopPreventionArray, $options, $userId)->delay( now()->addSeconds($diff) );
                continue;
            }

            if ($alreadyHasLabel === false && $time === null) {
                $order->labels()->attach($order->id, ['label_id' => $label->id, 'added_type' => $options['added_type'], 'created_at' => Carbon::now()]);
                self::setScheduledLabelsAfterAddition($order, $label, $userId);
                $loopPreventionArray['already-added'][] = $labelId;

                if ($label->message !== null && $labelId !== 89) {
                    LabelNotificationService::addLabelSentNotification($order, $label);
                }

                if ($label->id == 52) {  //wyslana do awizacji
                    LabelNotificationService::orderStatusChangeToDispatchNotification($order, $order->customer->id == 4128);
                }

                if ($label->id == Label::ORDER_ITEMS_CONSTRUCTED) {
                    LabelNotificationService::sendItemsConstructedMailJob($order);
                    self::savePackageGroup($order);

                    $tasks = Task::query()->where('order_id', '=', $order->id)->get();
                    if (count($tasks) != 0) {
                        foreach ($tasks as $task) {
                            $task->update([
                                'color' => '32CD32',
                                'status' => 'FINISHED'
                            ]);
                        }
                    }
                }

                if ($label->id == Label::ORDER_ITEMS_REDEEMED_LABEL) {
                    LabelNotificationService::sendItemsRedeemedMail($order);
                    $order->preferred_invoice_date = $now;
                    $tasks = Task::query()->where('order_id', '=', $order->id)->get();

                    if (count($tasks) != 0) {
                        foreach ($tasks as $task) {
                            $task->update([
                                'color' => '008000',
                                'status' => 'FINISHED'
                            ]);
                        }
                    }
                }
            }
        }

        if (count($labelsToAddAtTheEnd) > 0) {
            self::addLabels($order, $labelsToAddAtTheEnd, $loopPreventionArray, $options, $userId);
        }
        if (count($labelsToRemoveAtTheEnd) > 0) {
            RemoveLabelService::removeLabels($order, $labelsToRemoveAtTheEnd, $loopPreventionArray, [], $userId);
        }
    }

    private static function setScheduledLabelsAfterAddition(Order $order, Label $label, ?int $userId): void
    {
        $timedLabelsAfterAddition = $label->timedLabelsAfterAddition()->get();

        if (!count($timedLabelsAfterAddition)) {
            return;
        }

        /** @var Label $item */
        foreach ($timedLabelsAfterAddition as $item) {
            $pivot = $item->pivot;

            self::addScheduler($order, $label, $pivot, 'A', 'to_add_type_a');
            self::addScheduler($order, $label, $pivot, 'A', 'to_remove_type_a');
            self::addScheduler($order, $label, $pivot, 'B', 'to_add_type_b');
            self::addScheduler($order, $label, $pivot, 'B', 'to_remove_type_b');

            self::awaitForUserToEnterDate($order, $pivot, 'to_add_type_c', $userId);
            self::awaitForUserToEnterDate($order, $pivot, 'to_remove_type_c', $userId);
        }
    }

    private static function addScheduler(Order $order, Label $label, $pivot, string $type, string $action): void
    {
        $now = Carbon::now();
        if (!empty($pivot->$action)) {
            $minutesToAdd = (float)$pivot->$action * 60;
            OrderLabelScheduler::query()->create([
                'order_id' => $order->id,
                'label_id' => $label->id,
                'label_id_to_handle' => $pivot->label_to_handle_id,
                'type' => $type,
                'action' => $action,
                'trigger_time' => $now->addMinutes(ceil($minutesToAdd)),
            ]);
        }
    }

    private static function awaitForUserToEnterDate(Order $order, $pivot, string $action, ?int $userId): void
    {
        if (!empty($pivot->$action)) {
            OrderLabelSchedulerAwait::query()->create([
                'order_id' => $order->id,
                'user_id' => $userId,
                'labels_timed_after_addition_id' => $pivot->id,
            ]);
        }
    }

    private static function savePackageGroup(Order $order)
    {

        foreach ($order->packages as $package) {
            if (!empty($package->shipmentGroup)) {
                continue;
            }
            $searchCriteria = [
                'courier_name' => $package->delivery_courier_name,
                'shipment_date' => Carbon::now()->format('Y-m-d'),
            ];

            if ($package->service_courier_name === 'DPD') {
                if ($package->symbol === 'DPD_D_smart' || $package->symbol === 'DPD_d') {
                    $searchCriteria['package_type'] = 'Dłużyca';
                } else {
                    $searchCriteria['package_type'] = 'Standard';
                }
            } elseif ($package->service_courier_name === 'POCZTEX') {
                if (strpos($package->symbol, 'P_')) {
                    $searchCriteria['package_type'] = 'Paleta';
                } else {
                    $searchCriteria['package_type'] = 'Standard';
                }
            }

            //jeśli ze wczoraja nie jest
            $shipmentGroups = ShipmentGroup::query()->where($searchCriteria)->get();
            /** @var ?ShipmentGroup $shipmentGroup */
            $shipmentGroup = $shipmentGroups->filter(function (ShipmentGroup $shipmentGroup) {
                return $shipmentGroup->closed == false;
            })->first();
            if ($shipmentGroup === null) {
                $searchCriteria['lp'] = count($shipmentGroups) + 1;
                $shipmentGroup = ShipmentGroup::query()->create($searchCriteria);
            }
            $shipmentGroup->packages()->save($package);
        }
    }
}
