<?php

namespace App\Helpers;

use App\Entities\Chat;
use App\Entities\Label;

class OrderLabelHelper {

    public static function setRedLabel(Chat $chat) {
        $hasRed = $chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_RED_LABEL_ID)->count() > 0;
        $hasYellow = $chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_YELLOW_LABEL_ID)->count() > 0;
        if ($hasRed) {
            return;
        }
        if ($hasYellow) {
            $chat->order->labels()->detach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
        }
        $chat->order->labels()->attach(MessagesHelper::MESSAGE_RED_LABEL_ID, ['added_type' => Label::CHAT_TYPE]);
        $chat->need_intervention = true;
        $chat->save();
    }

    public static function setYellowLabel(Chat $chat) {
        $hasRed = $chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_RED_LABEL_ID)->count() > 0;
        $hasYellow = $chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_YELLOW_LABEL_ID)->count() > 0;
        if ($hasRed || $hasYellow) {
            return;
        }
        $chat->order->labels()->attach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID, ['added_type' => Label::CHAT_TYPE]);
        $chat->need_intervention = true;
        $chat->save();
    }

    public static function setBlueLabel(Chat $chat)
    {
        $chat->order->labels()->detach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
        if ($chat->order->labels()->where('label_id', MessagesHelper::MESSAGE_BLUE_LABEL_ID)->count() == 0) {
            $chat->order->labels()->attach(MessagesHelper::MESSAGE_BLUE_LABEL_ID, ['added_type' => Label::CHAT_TYPE]);
        }
    }
}
