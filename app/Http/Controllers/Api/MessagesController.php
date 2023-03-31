<?php

namespace App\Http\Controllers\Api;

use App\Entities\Chat;
use App\User;
use Exception;
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
use App\Http\Requests\Messages\PostMessageRequest;
use App\Http\Requests\Messages\GetMessagesRequest;
use App\Http\Requests\Api\Orders\ChatRequest;
use Illuminate\Http\JsonResponse;
use App\Helpers\GetCustomerForNewOrder;

class MessagesController extends Controller
{
    public function postNewMessage(PostMessageRequest $request, $token)
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
            $data = $request->validated();
            $file = $data['file'] ?? null;
            $helper->addMessage($data['message'], $data['area'], $file);
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

    public function removeUser(Request $request, string $token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chatId = $helper->getChat()->id;
            if ($request->type == ChatUser::class) {
                $chatUser = ChatUser::findOrFail($request->user_id);
            } else {
                $chatUser = $this->findCustomerOrEmployee($request, $chatId);
            }
            $chatUser->delete();
            return response('ok');
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    private function findCustomerOrEmployee(Request $request, int $chatId): ChatUser
    {
        if ($request->type == Customer::class) {
            $chatUser = ChatUser::where([
                'customer_id' => $request->user_id,
                'chat_id'     => $chatId,
                ])->first();
        }
        if ($request->type == Employee::class) {
            $chatUser = ChatUser::where([
                'employee_id' => $request->user_id,
                'chat_id'     => $chatId,
            ])->first();
        }
        return $chatUser;
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

    public function getMessages(GetMessagesRequest $request, string $token): Response
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();

            $data = $request->validated();
            $area = $data['area'] ?? 0;
            $lastId = $data['lastId'] ?? 0;

            if (!$chat) {
                throw new ChatException('Wrong chat token');
            }
            $assignedMessagesIds = json_decode($helper->getCurrentChatUser()->assigned_messages_ids ?: '[]', true);
            $assignedMessagesIds = array_flip($assignedMessagesIds);
            $out = '';
            foreach ($chat->messages as $message) {
                if ($message->id <= $lastId || $area != $message->area) {
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

    /**
     * Create chat used to contact between customer and consultant
     *
     * @param  ChatRequest  $request
     *
     * @return JsonResponse
     */
    public function createContactChat(ChatRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $helper = new MessagesHelper();
            $customerForNewOrder = new GetCustomerForNewOrder();
            $customer = $customerForNewOrder->getCustomer(null, $data);
            // get customer chats
            $possibleChatIds = ChatUser::where('customer_id', $customer->id)->get()->pluck('chat_id');

            if($possibleChatIds->isNotEmpty()) {
                // get contact chat for this customer, then add chat id to helper
                $contactChat = Chat::whereNull(['order_id', 'product_id'])->whereIn('id', $possibleChatIds)->first();
                if($contactChat !== null) {
                    $helper->chatId = $contactChat->id;
                }
            }
            $chatUserToken = $helper->getChatToken(null, $customer->id, MessagesHelper::TYPE_CUSTOMER);

            // if no previous chats, then create new one
            if(!$helper->chatId) {
                $helper->createNewChat();
            }

            return response()->json([
                'chatUserToken' => $chatUserToken,
            ]);
        } catch (ChatException $e) {
            $e->log();
        }

        return response()->json([
            'error' => 'Problem z utworzeniem nowego czatu dla klienta',
        ]);
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
