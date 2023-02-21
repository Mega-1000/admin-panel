<?php

namespace App\Services;

use App\Helpers\MessagesHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class MessagesService {

    public function prepareProductList(MessagesHelper $helper): Collection
    {
        if ($helper->getOrder()) {
            try {
                return $this->setProductsForChatUser($helper->getCurrentUser(), $helper->getOrder());
            } catch (\Exception $e) {
                Log::error('Cannot prepare product list',
                    ['exception' => $e->getMessage(), 'class' => $e->getFile(), 'line' => $e->getLine()]);
                return collect();
            }
        }

        if (!$helper->getProduct()) return collect();

        return collect([$helper->getProduct()]);
    }

    private function setProductsForChatUser($chatUser, $order)
    {
        if (is_a($chatUser, Employee::class)) {
            return $order->items->filter(function ($item) use ($chatUser) {
                return empty($item->product->firm) || $item->product->firm->id == $chatUser->firm->id;
            });
        }
        return $order->items;
    }
}
