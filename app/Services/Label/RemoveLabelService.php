<?php

namespace App\Services\Label;

use App\DTO\Label\LabelSessionRemoveLabelDTO;
use App\Entities\Label;
use App\Entities\Order;
use App\Entities\OrderWarehouseNotification;
use App\Entities\ProductStockLog;
use App\Entities\ProductStockPosition;
use App\Entities\WorkingEvents;
use App\Enums\ProductStockLogActionEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Jobs\TimedLabelJob;

class RemoveLabelService
{
    public static function removeLabels(Order $order, array $labelIdsToRemove, array &$loopPreventionArray, array $customLabelIdsToAddAfterRemoval, ?int $userId, ?string $time = null): array
    {

        WorkingEvents::createEvent(WorkingEvents::LABEL_REMOVE_EVENT, $order->id);

        if (count($labelIdsToRemove) < 1) {
            return [];
        }

        if (Auth::user() === null && $userId !== null) {
            Auth::loginUsingId($userId);
        }
        foreach ($labelIdsToRemove as $labelId) {
            if (array_key_exists('already-removed', $loopPreventionArray) && in_array($labelId, $loopPreventionArray['already-removed'])) {
                continue;
            }

            if ($labelId == 49 && Auth::user()?->role_id == 4) {
                continue;
            }

            if ($labelId == Label::PACKAGE_NOTIFICATION_SENT_LABEL) {
                OrderWarehouseNotification::query()->where('order_id', '=', $order->id)->delete();
            }

            /** @var Label $label */
            $label = Label::query()->find($labelId);

            if ($time !== null) {

                $preLabelId = DB::table('label_labels_to_add_after_timed_label')->where('main_label_id', $labelId)->first()?->label_to_add_id;

                if($preLabelId === null) continue;

                $now = Carbon::now();

                $order->labels()->detach($labelId);

                $order->labels()->attach($order->id, ['label_id' => $preLabelId, 'added_type' => NULL, 'created_at' => $now]);

                // // calc time to run timed label job
                $dateTo = new Carbon($time);
                $diff = $now->diffInSeconds($dateTo);

                TimedLabelJob::dispatch($labelId, $preLabelId, $order, $loopPreventionArray, [], $userId, $now)->delay( now()->addSeconds($diff) );
                continue;
            }

            if ($label->manual_label_selection_to_add_after_removal) {
                $labelIdsToAttach = $customLabelIdsToAddAfterRemoval;
            } else {
                $labelIdsToAttach = [];
                foreach ($label->labelsToAddAfterRemoval as $item) {
                    $labelIdsToAttach[] = $item->id;
                    if ($item->id == 50) {
                        $response = self::changeWarehouseStock($order, $userId);
                        if (array_key_exists('error', $response)) {
                            Session::put('removeLabelJobAfterProductStockMove', array_merge(
                                [new LabelSessionRemoveLabelDTO($order, $labelIdsToRemove, $loopPreventionArray, $customLabelIdsToAddAfterRemoval)],
                                Session::get('removeLabelJobAfterProductStockMove') ?? []));
                            return $response;
                        }

                        $order->labels()->detach($label);
                        $loopPreventionArray['already-removed'][] = $labelId;
                    }
                }
            }

            $order->labels()->detach($label);
            $loopPreventionArray['already-removed'][] = $labelId;

            if (count($labelIdsToAttach) > 0) {
                AddLabelService::addLabels($order, array_unique($labelIdsToAttach), $loopPreventionArray, [], $userId);
            }
            if (count($label->labelsToRemoveAfterRemoval) > 0) {
                $labelIdsToDetach = [];
                foreach ($label->labelsToRemoveAfterRemoval as $item) {
                    $labelIdsToDetach[] = $item->id;
                }
                self::removeLabels($order, array_unique($labelIdsToDetach), $loopPreventionArray, [], $userId);
            }
        }

        return ['success' => true];
    }

    private static function changeWarehouseStock(Order $order, ?int $userId): array
    {

        $errors = [];
        $productsIds = [];
        foreach ($order->items as $key => $item) {
            if (!isset($productsIds[$item->product->id])) {
                $productsIds[$item->product->id] = $key;
            } else {
                $order->items->get($productsIds[$item->product->id])->quantity += $item->quantity;
                $item->quantity = 0;
            }
        }

        $items = $order->items->filter(function ($item) {
            return $item->quantity > 0;
        });

        foreach ($items as $item) {
            $product = $item->product;
            if ($product !== null) {
                $productStockPosition = $product->stock->position->first();
                if (empty($productStockPosition)) {
                    $errors[] = ['error' => 'position', 'product' => $product->id, 'productName' => $product->symbol, 'productStock' => $product->stock];
                    continue;
                }

                $isMoreThanOnePosition = $product->stock->position->count() > 1;
                $hasPositionLessQuantityThanOrderQuantity = $productStockPosition->position_quantity < $item->quantity;

                if ($isMoreThanOnePosition && $hasPositionLessQuantityThanOrderQuantity) {
                    $errors[] = ['error' => 'quantity', 'product' => $product->id, 'productName' => $product->symbol, 'productStock' => $product->stock, 'position' => $productStockPosition];
                    continue;
                }

                $productStockLog = $product->stock->logs()->where('order_id', $order->id)->where('action', 'DELETE')->first();

                if (!empty($productStockLog)) {
                    $errors[] = ['error' => 'exists', 'product' => $product->id, 'productName' => $product->symbol, 'order_id' => $order->id];
                }
            }
        }

        if (count($errors) > 0) {
            return ['error' => $errors];
        }

        foreach ($items as $item) {
            $product = $item->product;
            if ($product === null) {
                return ['error' => 'Product does not exist.'];
            }
            $productStockLog = $product->stock->logs()->where('order_id', $order->id)->where('action', 'DELETE')->first();

            if (!empty($productStockLog)) {
                return ['error' => 'exists'];
            }
            if (!$item->packet) {
                $productStock = $product->stock;
                $productStock->update([
                    'quantity' => $productStock->quantity - $item->quantity,
                ]);

                /** @var ProductStockPosition $productStockPosition */
                $productStockPosition = $product->stock->position->first();
                $productStockPosition->update([
                    'position_quantity' => $productStockPosition->position_quantity - $item->quantity,
                ]);

                ProductStockLog::query()->create([
                    'product_stock_id' => $productStock->id,
                    'product_stock_position_id' => $productStockPosition->id,
                    'action' => ProductStockLogActionEnum::DELETE,
                    'quantity' => $item->quantity,
                    'order_id' => $order->id,
                    'user_id' => $userId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }

        return ['success' => true];
    }
}
