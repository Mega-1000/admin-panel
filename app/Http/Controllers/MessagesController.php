<?php

namespace App\Http\Controllers;

use App\Entities\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\MessagesHelper;
use Illuminate\Support\Collection;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Exceptions\ChatException;
use Barryvdh\Debugbar\Facades\Debugbar;
use App\Entities\Firm;
use App\Entities\Message;
use App\Enums\UserRole;

class MessagesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param bool $all
     * @param bool $orderId
     * @return Response
     */
    public static function index(Request $request, $all = false, $orderId = 0) {
        $chats = self::getChatView($all, $orderId, $request->user()->id);
        return view('chat.index')->withChats($chats)->withShowAll($all);
    }

    /**
     * @param bool $all
     * @param int $orderId
     * @param int $userId
     * @return array|Chat[]
     */
    public static function getChatView(bool $all, int $orderId, int $userId = null) {
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

    public static function getUrl(Request $request, $mediaId, $postCode, $email, $phone) {
        $url = self::getChatUrl($mediaId, $postCode, $email, $phone);
        return redirect($url);
    }

    public static function getChatUrl($mediaId, $postCode, $email, $phone): string {
        $token = MessagesHelper::getToken($mediaId, $postCode, $email, $phone);
        $url = route('chat.show', ['token' => $token]);
        return $url;
    }

    public function show($token) {
        if(config('app.env') == 'production') {
            Debugbar::disable();
        }
        try {
            return $this->prepareChatView($token);
        } catch (ChatException $e) {
            $e->log();
            // TODO Change to configuration
            return redirect(env('FRONT_URL'));
        }
    }
    /**
     * prepare chat View from given $token
     *
     * @param string $token
     * @return View
     */
    private function prepareChatView(string $token): View {
        $helper = new MessagesHelper($token);
        $chat = $helper->getChat();

        if($chat === null) {
            $helper->createNewChat();
            $chat = $helper->getChat();
        }

        // if no exist any user ID then bind one
        if($chat->user_id === null && $helper->currentUserType === MessagesHelper::TYPE_USER) {
            $userId = auth()->user()->id;
            $chat->user_id = $userId;
            $chat->save();
        }

        $product = $helper->getProduct();
        $order = $helper->getOrder();

        $chatType = $order ? 'order' : 'product';

        // create welcome message
        if($helper->currentUserType === MessagesHelper::TYPE_CUSTOMER && ($order?->need_support || $chat?->need_intervention)) {
            $blankChatUser = $helper->createOrGetBlankUser($chat);

            $content = "Witamy!
                        Konsultant zapoznaje się ze sprawą wkrótce się odezwie.
                        Zajmuje to zwykle do kilku minut.";
            $helper->addMessage($content, UserRole::Main, null, $blankChatUser);
        }
        // if exist order with need support then set need support to false, only for consultants
        if($order !== null && $order->need_support && $helper->currentUserType === MessagesHelper::TYPE_USER) {
            $order->need_support = false;
            $order->save();
        }
        if($order === null && $chat->need_intervention && $helper->currentUserType === MessagesHelper::TYPE_USER) {
            $chat->need_intervention = false;
            $chat->save();
        }

        $helper->setLastRead();

        $chatUsers = $chat->chatUsers;

        $chatEmployees   = $chatUsers->pluck('employee')->filter();
        $chatCustomers   = $chatUsers->pluck('customer')->filter();
        $chatConsultants = $chatUsers->pluck('user')->filter();

        $currentCustomersIdsOnChat = $chatCustomers->pluck('id');
        $currentEmployeesIdsOnChat = $chatEmployees->pluck('id');

        $possibleEmployees = collect();
        $possibleCustomers = collect();
        $productList = collect();
        $notices = '';
        if ($chatType == 'order') {
            $productList = $helper->prepareOrderItemsCollection($helper);
            $products = $productList->pluck('product');
        } else if ($chatType == 'product') {
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

        $assignedMessagesIds = [];
        if($currentChatUser !== null) {
            $assignedMessagesIds = json_decode($helper->getCurrentChatUser()->assigned_messages_ids ?: '[]', true);
        }

        $firmWithComplaintEmails = Firm::where('complaint_email', '<>', '')->get();

        $chatMessages = $chat->messages;
        
        $view = view('chat.show')->with([
            'product_list'            => $productList,
            'faq'                     => $this->prepareFaq($chatUsers),
            'notices'                 => $notices,
            'possibleEmployees'       => $possibleEmployees,
            'possibleCustomers'       => $possibleCustomers,
            'userType'                => $helper->currentUserType,
            'chatCustomers'           => $chatCustomers,
            'chatEmployees'           => $chatEmployees,
            'firmWithComplaintEmails' => $firmWithComplaintEmails,
            'chatConsultants'         => $chatConsultants,
            'chat'                    => $chat,
            'product'                 => $product,
            'order'                   => $order,
            'usersHistory'            => $usersHistory,
            'chatMessages'            => $chatMessages,
            'assignedMessagesIds'     => array_flip($assignedMessagesIds),
            'title'                   => $helper->getTitle(true),
            'route'                   => route('api.messages.post-new-message', ['token' => $token]),
            'routeAddUser'            => route('api.messages.add-new-user', ['token' => $token]),
            'routeCloseChat'          => route('api.messages.closeChat', ['token' => $token]),
            'routeRemoveUser'         => route('api.messages.remove-user', ['token' => $token]),
            'routeRefresh'            => route('api.messages.get-messages', ['token' => $token]),
            'routeCallComplaint'      => route('api.messages.callComplaint', ['token' => $token]),
            'routeAskForIntervention' => route('api.messages.ask-for-intervention', ['token' => $token]),
            'routeForEditPrices'      => route('api.messages.edit-prices', ['token' => $token])
        ]);
        return $view;
    }

    private function prepareFaq(Collection $users): array {
        $faqs = [];
        foreach ($users as $user) {
            if ($user->employee && $user->employee->faq) {
                $faqs[] = $user->employee->faq;
            }
        }
        return $faqs;
    }

    public function showOrNew(int $orderId, int $userId) {
        $chat = Chat::where('order_id', '=', $orderId)->first();
        if (!$chat) {
            $helper = new MessagesHelper();
            $helper->orderId = $orderId;
            $helper->currentUserId = $userId;
            $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
            $userToken = $helper->encrypt();
        } else {
            $helper = new MessagesHelper();
            $helper->chatId = $chat->id;
            $helper->currentUserId = $userId;
            $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
            $userToken = $helper->encrypt();
        }

        return redirect()->route('chat.show', ['token' => $userToken]);
    }
}
