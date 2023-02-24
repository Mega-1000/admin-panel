<?php

namespace App\Http\Controllers\Api;

use App\User;
use Exception;
use App\Entities\Chat;
use App\Entities\ChatUser;
use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderLabelHelper;
use App\Jobs\ChatNotificationJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Helpers\Exceptions\ChatException;

class MessagesController extends Controller
{
    public function postNewMessage(Request $request, $token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            if (!$chat) {
                $helper->createNewChat();
            }
            if (!$helper->canUserSendMessage()) {
                throw new ChatException('User not allowed to send message');
            }
            $area = $request->input('area') ?: 'default';
            $helper->addMessage($request->message, $area);
            $helper->setLastRead();
            return response('ok');
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    public function addUser(Request $request, $token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            if (!$chat) {
                $chat = $helper->createNewChat();
            }
            list($user, $chatUser) = $this->findCustomerOrEmployeeInTrash($request, $chat);
            if ($chatUser) {
                $chatUser->restore();
                return response('ok');
            }
            $this->createNewCustomerOrEmployee($chat, $request, $user);
            if (is_a($user, Customer::class)) {
                $email = $user->login;
                ChatNotificationJob::sendNewMessageEmail($email, $helper);
            }
            return response('ok');
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    private function findCustomerOrEmployeeInTrash(Request $request, $chat): array
    {
        if ($request->type == Customer::class) {
            $user = Customer::findOrFail($request->user_id);
            $chatUser = ChatUser::onlyTrashed()
                ->where('chat_id', $chat->id)
                ->where('customer_id', $user->id)
                ->whereNotNull('deleted_at')
                ->withTrashed()
                ->first();
        }
        if ($request->type == Employee::class) {
            $user = Employee::findOrFail($request->user_id);
            $chatUser = ChatUser::onlyTrashed()
                ->where('chat_id', $chat->id)
                ->where('employee_id', $user->id)
                ->whereNotNull('deleted_at')
                ->withTrashed()
                ->first();
        }
        return array($user, $chatUser);
    }

    /**
     * @param $chat
     * @param Request $request
     * @param $user
     */
    private function createNewCustomerOrEmployee($chat, Request $request, $user): void
    {
        $chatUser = new ChatUser();
        $chatUser->chat()->associate($chat);
        if ($request->type == Employee::class) {
            $chatUser->employee()->associate($user);
        }
        if ($request->type == Customer::class) {
            $chatUser->customer()->associate($user);
        }
        $chatUser->save();
    }

    public function removeUser(Request $request, $token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            if ($request->type == ChatUser::class) {
                $chatUser = ChatUser::findOrFail($request->user_id);
            } else {
                list($user, $chatUser) = $this->findCustomerOrEmployee($request, $chat);
            }
            $chatUser->delete();
            return response('ok');
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    private function findCustomerOrEmployee(Request $request, Chat $chat): array
    {
        if ($request->type == Customer::class) {
            $user = Customer::findOrFail($request->user_id);
            $chatUser = $chat->customers->first;
        }
        if ($request->type == Employee::class) {
            $user = Employee::findOrFail($request->user_id);
            $chatUser = $chat->employees->where('employee_id', $user->id)->withTrashed()->first();
        }
        return array($user, $chatUser);
    }

    public function askForIntervention($token)
    {
        try {
            $helper = new MessagesHelper($token);
            if (!$helper->getChat()) {
                throw new ChatException('Wrong chat token');
            }

            if ($helper->currentUserType == MessagesHelper::TYPE_USER) {
                $this->notifyEmployee($helper);
            } else {
                $this->notifyModerator($helper);
            }
            return response('ok');
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    private function notifyEmployee(MessagesHelper $helper)
    {
        $chat = $helper->getChat();
        if ($chat->employees->count() > 0) {
            $chat->employees->map(function ($user) use ($helper) {
                ChatNotificationJob::sendNewMessageEmail($user->email, $helper);
            });
        }
    }

    /**
     * @param MessagesHelper $helper
     */
    private function notifyModerator(MessagesHelper $helper): void
    {
        $chat = $helper->getChat();
        OrderLabelHelper::setRedLabel($chat);

        $chat->need_intervention = true;
        $chat->save();
        if ($chat->users->count() > 0) {
            $chat->users->map(function ($user) use ($helper) {
                ChatNotificationJob::sendNewMessageEmail($user->email, $helper);
            });
        } else {
            $user = User::where('name', '001')->first();
            ChatNotificationJob::sendNewMessageEmail($user->email, $helper);
        }
    }

    public function getMessages(Request $request, string $token): Response
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            $area = $request->input('area');
            if (!$chat) {
                throw new ChatException('Wrong chat token');
            }
            $assignedMessagesIds = json_decode($helper->getCurrentChatUser()->assigned_messages_ids, true);
            $assignedMessagesIds = array_flip($assignedMessagesIds);
            $out = '';
            foreach ($chat->messages as $message) {
                if ($message->id <= $request->lastId || $area != $message->area) {
                    continue;
                }
                if($helper->currentUserType == MessagesHelper::TYPE_USER || isset($assignedMessagesIds[$message->id] )) {
                    $out .= view('chat/single_message')->with([
                        'message' => $message,
                    ])->render();
                }
            }
            $helper->setLastRead();

            return response(['messages' => $out, 'users' => $chat->chatUsers]);
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    public function getHistory(Request $request)
    {
        $user = $request->user();
        $out = [];
        foreach ($user->chats as $chat) {
            $helper = new MessagesHelper();
            $helper->chatId = $chat->id;
            $helper->currentUserId = $user->id;
            $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
            $out[] = [
                'title' => $helper->getTitle(false),
                'url' => route('chat.show', ['token' => $helper->encrypt()]),
                'new_message' => $helper->hasNewMessage()
            ];
        }
        return response($out);
    }

    public function editPrices(Request $request, $token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            if (!$chat) {
                throw new ChatException('Nieprawidłowy token chatu');
            }
            $order = $helper->getOrder();
            if (!$order) {
                throw new ChatException('Nieprawidłowy token chatu');
            }
            $user = $helper->getCurrentUser();
            if (!(is_a($user, Employee::class) || is_a($user, User::class))) {
                throw new ChatException('Nieprawidłowy token chatu');
            }
            if (is_a($user, Employee::class)) {
                $firm = $user->firm();
                $orderFirm = $order->warehouse->firm;
                if ($firm->id != $orderFirm->id) {
                    throw new ChatException('Nieprawidłowy token chatu');
                }
            }
            $item = OrderItem::findOrFail($request->item_id);
            $item->net_purchase_price_commercial_unit_after_discounts = $request->commercial_price_net;
            $item->net_purchase_price_basic_unit_after_discounts = $request->basic_price_net;
            $item->net_purchase_price_calculated_unit_after_discounts = $request->calculated_price_net;
            $item->net_purchase_price_aggregate_unit_after_discounts = $request->aggregate_price_net;
            $item->save();
        } catch (ChatException $exception) {
            $exception->log();
            return response($exception->getMessage(), 400);
        } catch (Exception $exception) {
            error_log('test2');
            Log::error('Brak przedmiotu',
                ['message' => $exception->getMessage(), 'file' => $exception->getFile(), 'line' => $exception->getLine()]);
            return response('Edytowany przedmiot nie istnieje', 400);
        }
        $url = route('chat.show', ['token' => $token]);
        return redirect($url);
    }

    public function getUrl(Request $request)
    {
        try {
            $url = \App\Http\Controllers\MessagesController::getChatUrl($request->mediaId,
                $request->postCode,
                $request->email,
                $request->phone);
        } catch (ChatException $exception) {
            $exception->log();
            if ($exception->getMessage() == 'wrong_password') {
                return response(['errorMessage' => 'Podany email istnieje w naszej bazie. Proszę podać prawidłowe hasło/numer telefonu'], 400);
            } else {
                return response(['errorMessage' => 'Wystąpil błąd. Spróbuj ponownie później'], 400);
            }
        }
        return response(['url' => $url], 200);
    }
}
