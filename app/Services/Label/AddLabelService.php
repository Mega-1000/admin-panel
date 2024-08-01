<?php

namespace App\Services\Label;

use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderLabelScheduler;
use App\Entities\OrderLabelSchedulerAwait;
use App\Entities\ShipmentGroup;
use App\Entities\Task;
use App\Entities\WorkingEvents;
use App\Jobs\AvisationAcceptanceCheck;
use App\Services\AllegroPaymentsReturnService;
use App\Services\WorkingEventsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Jobs\TimedLabelJob;
use App\Services\EmailSendingService;
use App\Entities\EmailSetting;

class AddLabelService
{
    /**
     * @throws Exception
     */
    public static function addLabels(Order $order, array $labelIdsToAdd, array &$loopPreventionArray, array $options, ?int $userId = null, ?Carbon $time = null): void
    {
        $now = Carbon::now();

        $order->labels_log .= PHP_EOL . 'Dodano etykietę' . 'id: ' . implode(',', $labelIdsToAdd) . ' ' . $now->format('Y-m-d H:i:s') . ' przez użtkownika: ' . auth()->user()?->name ?? 'system';
        $order->saveQuietly();

        $options = array_merge([
            'added_type' => null,
        ], $options);

        WorkingEventsService::createEvent(WorkingEvents::LABEL_ADD_EVENT, $order->id);
        if (count($labelIdsToAdd) < 1) {
            return;
        }

        foreach ($labelIdsToAdd as $labelId) {
            if ($order->labels->contains('id', $labelId)) {
                continue;
            }

            if ($labelId === 39) {
                throw new Exception('Nie można dodać etykiety 39');
                return;
            }

            if ($labelId == 39 && $order->labels->contains('id', 240)) {
                return;
            }

            if ($labelId === 66) {
                if ($order->preferred_invoice_date === null) {
                    $order->preferred_invoice_date = now();
                    $order->save();

                    $order->labels()->attach(41);
                }

                if (!$order->labels()->where('label_id', 42)->exists()) {
                    !$order->labels()->where('label_id', 41)->exists() ? $order->labels()->attach(41) : null;
                    !$order->labels()->where('label_id', 195)->exists() ? $order->labels()->attach(195) : null;
                }
            }

            if (array_key_exists('already-added', $loopPreventionArray)
                && in_array($labelId, $loopPreventionArray['already-added'])) {
                continue;
            }

            /** @var Label $label */
            $label = Label::query()->find($labelId);
            // init timed labels
            if ($time !== null) {
                $preLabelId = DB::table('label_labels_to_add_after_timed_label')->where('main_label_id', $labelId)->first()?->label_to_add_id;
                if($preLabelId === null) continue;

                $alreadyHasLabel = $order->labels()->where('label_id', $preLabelId)->exists();

                $now = Carbon::now();
                if($alreadyHasLabel) {
                    $order->labels()->updateExistingPivot($preLabelId, ['label_id' => $preLabelId, 'added_type' => $options['added_type'], 'created_at' => $now]);
                } else {
                    $order->labels()->attach($preLabelId, ['label_id' => $preLabelId, 'added_type' => $options['added_type'], 'created_at' => $now]);
                }

                // calc time to run timed label job
                $dateTo = new Carbon($time);
                $diff = $now->diffInSeconds($dateTo);

                TimedLabelJob::dispatch($labelId, $preLabelId, $order, $loopPreventionArray, $options, $userId, $now)->delay( now()->addSeconds($diff) );
                continue;
            }

            $alreadyHasLabel = $order->labels()->where('label_id', $labelId)->exists();

            if ($label->id === 50) {
                EmailSetting::find(36)?->sendEmail($order->customer->login, $order);
            }

            if ($time === null) {
                if($alreadyHasLabel) {
                    $order->labels()->detach($labelId);
                }

                foreach($label->labelsToRemoveAfterAddition as $labelToRemove) {
                    $order->labels()->detach($labelToRemove->id);
                }
                foreach($label->labelsToAddAfterAddition as $labelToAdd) {
                    $order->labels()->attach($order->id, [
                        'label_id'   => $labelToAdd->id,
                        'added_type' => $options['added_type'],
                        'created_at' => Carbon::now()
                    ]);
                }

                $order->labels()->attach($order->id, [
                    'label_id'   => $label->id,
                    'added_type' => $options['added_type'],
                    'created_at' => Carbon::now()
                ]);

                self::setScheduledLabelsAfterAddition($order, $label, $userId);
                $loopPreventionArray['already-added'][] = $labelId;

                if ($label->message !== null && $labelId !== 89) {
                    LabelNotificationService::addLabelSentNotification($order, $label);
                }

                if ($label->id == 52) {  //wyslana do awizacji
                    LabelNotificationService::orderStatusChangeToDispatchNotification($order, $order->customer->id == 4128);
                    $now = now();
                    if ($now->isSaturday()) {
                        $delay = $now->copy()->next(Carbon::MONDAY)->hour(10)->minute(0);
                    } else {
                        // Add a two-hour delay
                        $delay = $now->copy()->addHours(2);

                        // If the time after two hours is not within working hours, adjust accordingly
                        if ($delay->hour >= 17) {
                            // Move to the next day if after 5 PM
                            $delay->addDay()->hour(8)->minute(0);
                        } elseif ($delay->hour < 8) {
                            // Set to 8 AM if before 8 AM
                            $delay->hour(8)->minute(0);
                        }

                        // If next day is Saturday, skip to Monday
                        if ($delay->isSaturday()) {
                            $delay->next(Carbon::MONDAY)->hour(10)->minute(0);
                        }
                    }

                    dispatch(new AvisationAcceptanceCheck($order))->delay($delay);

                }

                if ($label->id == Label::ORDER_ITEMS_CONSTRUCTED) {
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
                    $emailSendingService = new EmailSendingService();
                    $emailSendingService->addNewScheduledEmail($order, EmailSetting::PICKED_UP);
                    $emailSendingService->addNewScheduledEmail($order, EmailSetting::PICKED_UP_2);

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

        AllegroPaymentsReturnService::checkAllegroReturn($order);
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

    public static function addScheduler(Order $order, Label $label, $pivot, string $type, string $action): void
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

    private static function savePackageGroup(Order $order): void
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
