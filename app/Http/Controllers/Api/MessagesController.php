<?php

namespace App\Http\Controllers\Api;

use App\DTO\Messages\CreateMessageDTO;
use App\Entities\ChatUser;
use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderLabelHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Orders\ChatRequest;
use App\Http\Requests\Messages\CustomerComplaintRequest;
use App\Http\Requests\Messages\GetMessagesRequest;
use App\Http\Requests\Messages\PostMessageRequest;
use App\Jobs\ChatNotificationJob;
use App\Repositories\Chats;
use App\Services\Label\AddLabelService;
use App\Services\MessageService;
use App\User;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class MessagesController extends Controller
{
    public function __construct(
        readonly protected MessageService $messageService,
    ) {}

    public function postNewMessage(PostMessageRequest $request, string $token): Response|Application|ResponseFactory
    {
        $msgTemplate = '';
        try {
            $message = $this->messageService->addMessage(
                CreateMessageDTO::fromRequest($request->validated(), $token),
            );
            $msgTemplate .= view('chat/single_message')->with([
                'message' => $message,
            ])->render();

            $apiUrl = "https://api.anthropic.com/v1/messages";
            $apiKey = "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";
            $anthropicVersion = "2023-06-01";

            $data = [
                "model" => "claude-3-opus-20240229",
                "max_tokens" => 1024,
                "messages" => [
                    ["role" => "user", "content" => 'You are part of my larvel system. You have to detect if user wants to add employee of company to the chat if so provide me json response like this
{ "AddCompany": "COMPANY NAME", "NoticeForUser": "change it to message for user", }
if user wants to add some compoany wich is not in list provide response like this { "NoticeForUser": "change it to message for user", }
There are only these companies: "IZOTERM" "POLSTYR" "SWISSPOR"
There is also possibiliy to change date of spedition in this case you have to return response like this
{ "ChangeDates": "from: 25.05.2024 to: 30.05.2024", "NoticeForUser": "Zmieniłem daty klienta na: od 25.05.2024 do 30.05.2024", }
If user wants to perform onne of this actions to add otherwise return "No message" If you want to send message to user because user wants to perform one of actions but for example you need more info provide response replace notice for user with your message to get more into { "NoticeForUser": "change it to message for user", }
Is everything certain?
Do not provide any other type response it will break system
user prompt: "siemka naklejka dodajta mi izoterma do chata"
']
                ]
            ];

            $payload = json_encode($data);

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "x-api-key: $apiKey",
                "anthropic-version: $anthropicVersion",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            } else {
                try {
                $response = json_decode(str_replace(',
}', '}', json_decode(dd($response))->content[0]->text));


                if ($response->NoticeForUser) {
                    $dto =  CreateMessageDTO::fromRequest($request->validated(), $token);
                    $dto->message = $response->NoticeForUser;
                    $dto->

                    $message = $this->messageService->addMessage(
                        $dto
                    );

                    $msgTemplate .= view('chat/single_message')->with([
                        'message' => $message,
                    ])->render();
                }

                } catch (\Exception $exception) {
dd($exception);
                }
            }

            curl_close($ch);

            return response($msgTemplate);
        } catch (ChatException $e) {
            $e->log();
            return response($e->getMessage(), 400);
        }
    }

    public function addUser(Request $request, $token): string|JsonResponse
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
                return 'ok';
            }

            MessageService::createNewCustomerOrEmployee($chat, $request, $user);

            if (is_a($user, Customer::class)) {
                $email = $user->login;
                ChatNotificationJob::sendNewMessageEmail($email, $helper);
            }

            return 'ok';
        } catch (ChatException $e) {
            $e->log();
            return response()->json($e->getMessage(), 400);
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

    public function removeUser(Request $request, string $token): string|JsonResponse
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
            return 'ok';
        } catch (ChatException $e) {
            $e->log();
            return response()->json($e->getMessage(), 400);
        }
    }

    private function findCustomerOrEmployee(Request $request, int $chatId): ChatUser
    {
        if ($request->type == Customer::class) {
            $chatUser = ChatUser::where([
                'customer_id' => $request->user_id,
                'chat_id' => $chatId,
            ])->first();
        }

        if ($request->type == Employee::class) {
            $chatUser = ChatUser::where([
                'employee_id' => $request->user_id,
                'chat_id' => $chatId,
            ])->first();
        }

        return $chatUser;
    }

    public function askForIntervention($token): Response|Application|ResponseFactory
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

            return response()->json('ok');
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

    /**
     * @throws Exception
     */
    public function getMessages(GetMessagesRequest $request, string $token): JsonResponse
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();

            $data = $request->validated();
            $area = $data['area'] ?? 0;
            $lastId = $data['lastId'] ?? 0;

            if (!$chat) {
                throw new Exception('Wrong chat token');
            }

            $assignedMessagesIds = json_decode($helper->getCurrentChatUser()->assigned_messages_ids ?: '[]', true);
            $assignedMessagesIds = array_flip($assignedMessagesIds);
            $out = '';
            foreach ($chat->messages as $message) {
                if ($message->id <= $lastId || $area != $message->area) {
                    continue;
                }
                if ($helper->currentUserType == MessagesHelper::TYPE_USER || isset($assignedMessagesIds[$message->id])) {
                    $out .= view('chat/single_message')->with([
                        'message' => $message,
                    ])->render();
                }
            }
            $helper->setLastRead();

            return response()->json(['messages' => $out, 'users' => $chat->chatUsers]);
        } catch (ChatException $e) {
            $e->log();
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Create chat used to contact between customer and consultant
     *
     * @param int $orderId
     * @param ChatRequest $request
     *
     * @return JsonResponse
     */
    public function createContactChat(int $orderId, Request $request): JsonResponse
    {
        $questionsTree = $request->input('questionsTree') ?: '';
        $customer = Order::findOrFail($orderId)->customer;

        try {
            $helper = new MessagesHelper();
            // get customer chats
            $customerChatIds = Chats::getCustomerChats($customer->id);
            $chat = null;

            if ($customerChatIds->isNotEmpty()) {
                // get contact chat for this customer, then add chat id to helper
                $contactChat = Chats::getContactChat($customerChatIds);
                if ($contactChat !== null) {
                    $helper->chatId = $contactChat->id;
                    $chat = $contactChat;
                }
            }
            $chatUserToken = $helper->getChatToken(null, $customer->id, MessagesHelper::TYPE_CUSTOMER);

            if ($chat === null) {
                $chat = $helper->createNewChat();
            }
            $chat->questions_tree = $questionsTree;
            $chat->save();

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

    /**
     * Create Chat for Customer Complaint
     *
     * @param CustomerComplaintRequest $request
     * @param Order $order
     * @param MessagesHelper $helper
     * @return JsonResponse
     */
    public function createCustomerComplaintChat(CustomerComplaintRequest $request, Order $order, MessagesHelper $helper): JsonResponse
    {
        $complaintForm = $request->validated();
        $customer = $order->customer;

        $arr = [];
        AddLabelService::addLabels($order, [59], $arr, []);

        DB::transaction(function () use ($request, $order, $customer, $helper, $complaintForm, &$chatUserToken) {
            $chat = $order->chat;

            if ($chat !== null) {
                $helper->chatId = $chat->id;
            }

            $chatUserToken = $helper->getChatToken($order->id, $customer->id, MessagesHelper::TYPE_CUSTOMER);

            $chat = $chat === null ? $helper->createNewChat() : $chat;

            if ($request->hasFile('image')) {
                $imagePaths = [];

                foreach ($request->file('image') as $imageFile) {
                    $originalFileName = $imageFile->getClientOriginalName();
                    $hashedFileName = Hash::make($originalFileName);
                    $path = $imageFile->storeAs('chat_files/' . $chat->id, $hashedFileName, 'public');

                    if ($path) {
                        $imagePaths[] = [
                            'path' => $path,
                            'name' => $originalFileName,
                        ];
                    }
                }

                $complaintForm['images'] = $imagePaths;
            }


            $chat->complaint_form = json_encode($complaintForm);
            $chat->save();
        });

        if (!isset($chatUserToken)) {
            return response()->json([
                'error' => 'Problem z utworzeniem nowego czatu dla klienta',
            ], 500);
        }

        return response()->json([
            'chatUserToken' => $chatUserToken,
        ]);
    }

    /**
     * Close chat
     *
     * @param string $token
     *
     * @return void
     * @throws Exception
     */
    public function closeChat(string $token): void
    {
        $helper = new MessagesHelper($token);
        $chat = $helper->getChat();
        $order = $helper->getOrder();
        if ($chat === null) {
            throw new Exception('Nieprawidłowy token chatu');
        }
        $user = $helper->getCurrentUser();
        $currentChatUser = $helper->getCurrentChatUser();
        $currentChatUser->is_online = false;
        $currentChatUser->save();

        if ($helper->currentUserType === MessagesHelper::TYPE_CUSTOMER && $order !== null) {
            // close by client
            $loopPreventionArray = [];
            AddLabelService::addLabels($order, [$helper::MESSAGE_GREEN_LABEL_ID], $loopPreventionArray, [], $user->id);

        } else if ($helper->currentUserType === MessagesHelper::TYPE_USER) {
            // close by consultant
            $chat->user_id = null;
            $chat->save();

            if ($order !== null) {
                $loopPreventionArray = [];
                AddLabelService::addLabels($order, [$helper::MESSAGE_GREEN_LABEL_ID], $loopPreventionArray, [], $user->id);
            }
        }
    }

    /**
     * Send complaint email to employee
     *
     * @param Request $request
     * @param string $token
     *
     * @return void
     * @throws ChatException
     */
    public function callComplaint(Request $request, string $token): void
    {
        $helper = new MessagesHelper($token);
        $email = $request->input('email');

        $helper->sendComplaintEmail($email);
    }

    public function getHistory(Request $request): Response|Application|ResponseFactory
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

    public function editPrices(Request $request, $token): Response|Redirector|RedirectResponse|Application|ResponseFactory
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
            return response()->json('Edytowany przedmiot nie istnieje', 400);
        }
        $url = route('chat.show', ['token' => $token]);

        return redirect($url);
    }

    public function getUrl(Request $request): JsonResponse
    {
        try {
            $url = \App\Http\Controllers\MessagesController::getChatUrl($request->mediaId,
                $request->postCode,
                $request->email,
                $request->phone);
        } catch (ChatException $exception) {
            $exception->log();
            if ($exception->getMessage() == 'wrong_password') {
                return response()->json(['errorMessage' => 'Podany email istnieje w naszej bazie. Proszę podać prawidłowe hasło/numer telefonu'], 400);
            } else {
                return response()->json(['errorMessage' => 'Wystąpil błąd. Spróbuj ponownie później'], 400);
            }
        }

        return response()->json(['url' => $url], 200);
    }
}
