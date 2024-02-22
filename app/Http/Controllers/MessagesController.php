<?php

namespace App\Http\Controllers;

use App\Entities\Chat;
use App\Entities\Firm;
use App\Entities\Order;
use App\Exceptions\DeliverAddressNotFoundException;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use App\Jobs\ChatNotificationJob;
use App\Repositories\Chats;
use App\Services\MessageService;
use App\Services\ProductService;
use App\Services\StyrofoarmAuctionService;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class MessagesController extends Controller
{
    public function __construct(
        private readonly ProductService $productService,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param bool $all
     * @param int $orderId
     * @return Response
     */
    public static function index(Request $request, bool $all = false, int $orderId = 0): Response
    {
        $chats = self::getChatView($all, $orderId, $request->user()->id);

        return view('chat.index')->withChats($chats)->withShowAll($all);
    }

    /**
     * @param bool $all
     * @param int $orderId
     * @param int|null $userId
     * @return array|Chat
     */
    public static function getChatView(bool $all, int $orderId, int $userId = null): array|Chat
    {
        if ($all) {
            $chats = Chat::where('id', '>', 0);
        } else {
            $chats = Chat::whereHas('messages', function ($q) {
                $q->where('created_at', '>', now()->subDays(30));
            });
        }

        if ($orderId) {
            $chats->where('order_id', $orderId);
        }
        $chats = $chats->get();

        foreach ($chats as $chat) {
            $helper = new MessagesHelper();
            $helper->chatId = $chat->id;
            $helper->currentUserId = $userId ?? Auth::user()->id;
            $helper->currentUserType = MessagesHelper::TYPE_USER;
            $chat->has_new_message = $helper->hasNewMessage();
            $chat->title = $helper->getTitle(true);
            $chat->url = route('chat.show', ['token' => $helper->encrypt()]);
            $chat->lastMessage = $chat->messages()->latest()->first();
            if (empty($chat->lastMessage)) {
                $chat->lastMessage = (object)['created_at' => null, 'message' => ''];
            }
        }

        $chats = $chats->all();
        uasort($chats, function ($a, $b) {
            return $a->lastMessage->created_at > $b->lastMessage->created_at ? -1 : 1;
        });
        return $chats;
    }

    public static function getUrl(Request $request, $mediaId, $postCode, $email, $phone): RedirectResponse
    {
        $url = self::getChatUrl($mediaId, $postCode, $email, $phone);

        return redirect($url);
    }

    public static function getChatUrl($mediaId, $postCode, $email, $phone): string
    {
        $token = MessagesHelper::getToken($mediaId, $postCode, $email, $phone);

        return route('chat.show', ['token' => $token]);
    }

    public function show($token)
    {
        if (config('app.env') == 'production') {
            Debugbar::disable();
        }

        try {
            return $this->prepareChatView($token);
        } catch (ChatException $e) {
            $e->log();
            return redirect(config('app.front_url'));
        }
    }

    /**
     * prepare chat View from given $token
     *
     * @param string $token
     * @return View
     * @throws ChatException
     * @throws DeliverAddressNotFoundException
     */
    private function prepareChatView(string $token): View
    {
        $helper = new MessagesHelper($token);
        $chat = $helper->getChat();

        if ($chat === null) {
            $helper->createNewChat();
            $chat = $helper->getChat();
        }

        // if no exist any user ID then bind one
        if ($chat->user_id === null && $helper->currentUserType === MessagesHelper::TYPE_USER) {
            $userId = auth()->user()->id;
            $chat->user_id = $userId;
            $chat->save();
        }

        $product = $helper->getProduct();
        $order = $helper->getOrder();

        $chatType = $order ? 'order' : 'product';

        // if exist order with need support then set need support to false, only for consultants
        if ($order !== null && $order->need_support && $helper->currentUserType === MessagesHelper::TYPE_USER) {
            $order->need_support = false;
            $order->save();
        }
        if ($order === null && $chat->need_intervention && $helper->currentUserType === MessagesHelper::TYPE_USER) {
            $chat->need_intervention = false;
            $chat->save();
        }

        $helper->setLastRead();

        $chatUsers = $chat->chatUsers;

        $chatEmployees = $chatUsers->pluck('employee')->filter();
        $chatCustomers = $chatUsers->pluck('customer')->filter();
        $chatConsultants = $chatUsers->pluck('user')->filter();
        $chatBlankUser = Chats::getBlankChatUser($chatUsers);

        $currentCustomersIdsOnChat = $chatCustomers->pluck('id');
        $currentEmployeesIdsOnChat = $chatEmployees->pluck('id');

        $possibleEmployees = collect();
        $possibleCustomers = collect();
        $notices = '';

        if ($chatType == 'order') {
            $productList = $helper->prepareOrderItemsCollection($helper);
            $products = $productList->pluck('product');
        } else {
            $productList = $products = collect([$helper->getProduct()]);
        }

        $employeesIds = $products->pluck('employees_ids');

        if ($employeesIds->isNotEmpty()) {
            $possibleEmployees = $helper->prepareEmployees($employeesIds, $currentEmployeesIdsOnChat);
        }
        if ($currentCustomersIdsOnChat->isEmpty() && $chatType == 'order') {
            $possibleCustomers = collect([$order->customer]);
        }

        $usersHistory = [
            'customers' => $chat->chatUsersWithTrashed()->whereNotNull('customer_id')->get(),
            'employees' => $chat->chatUsersWithTrashed()->whereNotNull('employee_id')->get(),
            'consultants' => $chat->chatUsersWithTrashed()->whereNotNull('user_id')->get(),
        ];

        $token = $helper->encrypt();

        $currentChatUser = $helper->getCurrentChatUser();

        $currentChatUser->is_online = true;

        $assignedMessagesIds = [];
        if ($currentChatUser !== null) {
            $assignedMessagesIds = json_decode($helper->getCurrentChatUser()->assigned_messages_ids ?: '[]', true);
        }
        $currentChatUser->save();

        $firmWithComplaintEmails = Firm::where('complaint_email', '<>', '')->get();

        $chatMessages = $chat->messages;

        $isStyrofoarm = false;
        foreach ($products as $product) {
            if ($product?->variation_group === 'styropiany') {
                $isStyrofoarm = true;
                break;
            }
        }

        $allEmployeesFromRelatedOrders = [];
        if (isset($order) && get_class($order) === Order::class && $isStyrofoarm) {
            StyrofoarmAuctionService::updateAuction($chat, $products);

            $allEmployeesFromRelatedOrders = $this->productService->getUsersFromVariations($order);
        }

        return view('chat.show', [
            'isStyropian' => $isStyrofoarm,
            'product_list' => $productList,
            'faq' => $this->prepareFaq($chatUsers),
            'notices' => $notices,
            'possibleEmployees' => $possibleEmployees,
            'possibleCustomers' => $possibleCustomers,
            'userType' => $helper->currentUserType,
            'chatCustomers' => $chatCustomers,
            'chatEmployees' => $chatEmployees,
            'chatBlankUser' => $chatBlankUser,
            'firmWithComplaintEmails' => $firmWithComplaintEmails,
            'chatConsultants' => $chatConsultants,
            'chat' => $chat,
            'product' => $product,
            'order' => $order,
            'usersHistory' => $usersHistory,
            'chatMessages' => $chatMessages,
            'assignedMessagesIds' => array_flip($assignedMessagesIds),
            'title' => $helper->getTitle(true),
            'route' => route('api.messages.post-new-message', ['token' => $token]),
            'routeAddUser' => route('api.messages.add-new-user', ['token' => $token]),
            'routeCloseChat' => route('api.messages.closeChat', ['token' => $token]),
            'routeRemoveUser' => route('api.messages.remove-user', ['token' => $token]),
            'routeRefresh' => route('api.messages.get-messages', ['token' => $token]),
            'routeCallComplaint' => route('api.messages.callComplaint', ['token' => $token]),
            'routeAskForIntervention' => route('api.messages.ask-for-intervention', ['token' => $token]),
            'routeForEditPrices' => route('api.messages.edit-prices', ['token' => $token]),
            'companies' => Firm::all(),
        ], compact('allEmployeesFromRelatedOrders') ?? []);
    }

    private function prepareFaq(Collection $users): array
    {
        $faqs = [];
        foreach ($users as $user) {
            if ($user->employee && $user->employee->faq) {
                $faqs[] = $user->employee->faq;
            }
        }
        return $faqs;
    }

    public function showOrNew(int $orderId, int $userId): RedirectResponse
    {
        $chat = Chat::where('order_id', '=', $orderId)->first();
        $helper = new MessagesHelper();

        if (!$chat) {
            $helper->orderId = $orderId;
        } else {
            $helper->chatId = $chat->id;
        }

        $helper->currentUserId = $userId;
        $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
        $userToken = $helper->encrypt();

        $showAuctionInstructions = request()->query('showAuctionInstructions');

        return redirect()->route('chat.show', ['token' => $userToken, 'showAuctionInstructions' => $showAuctionInstructions]);
    }

    /**
     * @throws ChatException
     */
    public function addUsersFromCompanyToChat(Chat $chat, Request $request): RedirectResponse
    {
        $company = Firm::where('symbol', $request->get('firm_symbol'))->first();

        foreach ($company->employees as $employee) {
            $chatHelper = new MessagesHelper($chat->token);

            $chatHelper->chatId = $chat->id;
            $chatHelper->currentUserType = 'e';

            $userId = MessageService::createNewCustomerOrEmployee($chat, new Request(['type' => 'Employee']), $employee);
            $chatHelper->currentUserId = $userId;

            ChatNotificationJob::sendNewMessageEmail($employee->email, $chatHelper);
        }

        return redirect()->back();
    }
}
