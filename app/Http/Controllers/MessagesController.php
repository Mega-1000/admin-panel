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
     * @param bool $orderId
     * @param null $userId
     * @return array|Chat[]
     */
    public static function getChatView(bool $all, $orderId, $userId = null) {
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

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request) {
        //
    }

    public function show($token) {
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
        $product = $helper->getProduct();
        $order = $helper->getOrder();

        $chatType = $order ? 'order' : 'product';

        $helper->setLastRead();

        $chatUsers = empty($chat) ? collect() : $chat->chatUsers;

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

        // if ($product) {
        //     // get possible users from company / firm
        //     $possibleUsers = $this->getNotAttachedChatUsersForProduct($product, $chat->customers->first());
        // } else if ($order) {
        //     // get possible users from company / firm
        //     $possibleUsers = $this->getNotAttachedChatUsersForOrder($order);
        //     if ($helper->currentUserType == MessagesHelper::TYPE_USER || $helper->currentUserType == MessagesHelper::TYPE_EMPLOYEE) {
        //         $notices = $order->consultant_notices;
        //     }
        // }
        // get possible users from customerChatList
        // $possibleUsers = $this->addCustomerToChatList($chat, $possibleUsers, $users, $helper);

        $token = $helper->encrypt();

        $assignedMessagesIds = json_decode($helper->getCurrentChatUser()->assigned_messages_ids, true);

        $view = view('chat.show')->with([
            'product_list'            => $productList,
            'faq'                     => $this->prepareFaq($chatUsers),
            'notices'                 => $notices,
            'possibleEmployees'       => $possibleEmployees,
            'possibleCustomers'       => $possibleCustomers,
            'userType'                => $helper->currentUserType,
            'chatCustomers'           => $chatCustomers,
            'chatEmployees'           => $chatEmployees,
            'chatConsultants'         => $chatConsultants,
            'chat'                    => $chat,
            'product'                 => $product,
            'order'                   => $order,
            'usersHistory'            => $usersHistory,
            'area'                    => request()->get('area', 0),
            'assignedMessagesIds'     => array_flip($assignedMessagesIds),
            'title'                   => $helper->getTitle(true),
            'route'                   => route('api.messages.post-new-message', ['token' => $token]),
            'routeAddUser'            => route('api.messages.add-new-user', ['token' => $token]),
            'routeRemoveUser'         => route('api.messages.remove-user', ['token' => $token]),
            'routeRefresh'            => route('api.messages.get-messages', ['token' => $token]),
            'routeAskForIntervention' => route('api.messages.ask-for-intervention', ['token' => $token]),
            'routeForEditPrices'      => route('api.messages.edit-prices', ['token' => $token])
        ]);
        return $view;
    }

    // /**
    //  * @param $product
    //  * @param $chat
    //  * @param Collection $possibleUsers
    //  * @param Collection $users
    //  * @return Collection
    //  */
    // private function getNotAttachedChatUsersForProduct($product, $customer): Collection
    // {
    //     $possibleUsers = collect();
    //     foreach ($product->media()->get() as $media) {
    //         $mediaData = explode('|', $media->url);
    //         if (count($mediaData) != 3) {
    //             continue;
    //         }
    //         if ($customer->standardAddress()) {
    //             $codeObj = PostalCodeLatLon::where('postal_code', $customer->standardAddress()->postal_code)->first();
    //         } else {
    //             continue;
    //         }

    //         $availableUser = $media->product->firm->employees->filter(function ($employee) use ($codeObj) {
    //             $dist = MessagesHelper::calcDistance($codeObj->latitude, $codeObj->longitude, $employee->latitude, $employee->longitude);
    //             return $dist < $employee->radius;
    //         });
    //         $possibleUsers = $possibleUsers->merge($availableUser);
    //     }
    //     $possibleUsers = $possibleUsers->unique('id');
    //     return $possibleUsers;
    // }

    // /**
    //  * @param $order
    //  * @param $chat
    //  * @param Collection $users
    //  * @return array
    //  */
    // private function getNotAttachedChatUsersForOrder($order): Collection
    // {
    //     $possibleUsers = collect();
    //     foreach ($order->items as $product) {
    //         $firm = Firm::where('symbol', 'like', $product->product->product_name_supplier)->first();
    //         if (empty($firm)) {
    //             continue;
    //         }
    //         $possibleUsers = $possibleUsers->merge($firm->employees);
    //     }
    //     return $possibleUsers;
    // }

    // private function addCustomerToChatList(Chat|null $chat, Collection $possibleUsers, Collection $users, MessagesHelper $helper): Collection
    // {
    //     if ($chat) {
    //         $possibleUsers = $this->filterPossibleUsersWithCurrentlyAdded($possibleUsers, $chat, $users);
    //         if ($chat->customers()->whereNull('deleted_at')->count() < 1) {
    //             if ($helper->getOrder()) {
    //                 $customer = $helper->getOrder()->customer;
    //                 $possibleUsers->push($customer);
    //             }
    //             if ($helper->getProduct()) {
    //                 $possibleUsers->push($chat->customers()->first());
    //             }
    //         }
    //     }
    //     if ($helper->getOrder()) {
    //         $customer = $helper->getOrder()->customer;
    //         $possibleUsers->push($customer);
    //     }
    //     return $possibleUsers;
    // }

    // /**
    //  * @param Collection $possibleUsers
    //  * @param $chat
    //  * @param Collection $users
    //  * @return Collection
    //  */
    // private function filterPossibleUsersWithCurrentlyAdded(Collection $possibleUsers, $chat, Collection $users): Collection
    // {
    //     $possibleUsers = $possibleUsers->filter(function ($item) use ($chat, $users) {
    //         $filteredEmployeesCount = $chat->chatUsersWithTrashed->filter(function ($user) use ($item) {
    //             if (empty($user->employee)) {
    //                 return false;
    //             }
    //             return $item->id != $user->employee->id || $item->id == $user->employee->id && $user->trashed();
    //         })->count();
    //         return $filteredEmployeesCount == $chat->employees->count();
    //     });
    //     return $possibleUsers;
    // }

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

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
    public function edit(int $id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     */
    public function update(Request $request, int $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
    public function destroy(int $id) {
        //
    }
}
