<?php

namespace App\Helpers;

use App\Entities\Chat;
use App\Entities\Label;

class OrderLabelHelper {

    public static function setRedLabel(Chat $chat): void
    {
        $hasRed = $chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_RED_LABEL_ID)->count() > 0;
        $hasYellow = $chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_YELLOW_LABEL_ID)->count() > 0;
        if ($hasRed) {
            return;
        }
        if ($hasYellow) {
            $chat->order->labels()->detach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
        }
        $chat->order->labels()->attach(MessagesHelper::MESSAGE_RED_LABEL_ID, ['added_type' => Label::CHAT_TYPE]);
        $chat->save();
    }

    public static function setYellowLabel(Chat $chat): void
    {
        $hasRed = $chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_RED_LABEL_ID)->count() > 0;
        $hasYellow = $chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_YELLOW_LABEL_ID)->count() > 0;
        if ($hasRed || $hasYellow) {
            return;
        }
        $chat->order->labels()->detach(MessagesHelper::MESSAGE_BLUE_LABEL_ID);
        $chat->order->labels()->attach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID, ['added_type' => Label::CHAT_TYPE]);
        $chat->save();
    }

    public static function setBlueLabel(Chat $chat): void
    {
        $chat->order->labels()->detach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
        $chat->order->labels()->detach(144);
        if ($chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_BLUE_LABEL_ID)->count() == 0) {
            $chat->order->labels()->attach(MessagesHelper::MESSAGE_BLUE_LABEL_ID, ['added_type' => Label::CHAT_TYPE]);
        }
    }
}
