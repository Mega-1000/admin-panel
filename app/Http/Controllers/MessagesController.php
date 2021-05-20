<?php

namespace App\Http\Controllers;

use App\Entities\Employee;
use App\Entities\Firm;
use App\Entities\PostalCodeLatLon;
use App\User;
use Illuminate\Http\Request;
use App\Helpers\MessagesHelper;
use App\Helpers\Exceptions\ChatException;
use App\Entities\Chat;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param bool $all
     * @param bool $orderId
     * @return Response
     */
    public static function index(Request $request, $all = false, $orderId = 0)
    {
        $chats = self::getChatView($all, $orderId, $request->user()->id);
        return view('chat.index')->withChats($chats)->withShowAll($all);
    }

    /**
     * @param bool $all
     * @param bool $orderId
     * @param Request $request
     * @return mixed
     */
    public static function getChatView(bool $all, $orderId, $userId = null)
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
                $chat->lastMessage = (object) ['created_at' => null, 'message' => ''];
            }
        }

        $chats = $chats->all();
        uasort($chats, function ($a, $b) {
            return $a->lastMessage->created_at > $b->lastMessage->created_at ? -1 : 1;
        });
        return $chats;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    public function show($token)
    {
        try {
            return $this->prepareChatView($token);
        } catch (ChatException $e) {
            $e->log();
            return redirect(env('FRONT_URL'));
        }
    }

    public function showOrNew(int $orderId, int $userId)
    {
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
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public static function getUrl(Request $request, $mediaId, $postCode, $email, $phone)
    {
        try {
            $url = self::getChatUrl($mediaId, $postCode, $email, $phone);
            return redirect($url);
        } catch (ChatException $e) {
            $e->log();
            return redirect(env('FRONT_URL'));
        }
    }

    public static function getChatUrl($mediaId, $postCode, $email, $phone): string
    {
        $token = MessagesHelper::getToken($mediaId, $postCode, $email, $phone);
        $url = route('chat.show', ['token' => $token]);
        return $url;
    }

    /**
     * @param $product
     * @param $chat
     * @param Collection $possibleUsers
     * @param Collection $users
     * @return Collection
     */
    private function getNotAttachedChatUsersForProduct($product, $customer): Collection
    {
        $possibleUsers = collect();
        foreach ($product->media()->get() as $media) {
            $mediaData = explode('|', $media->url);
            if (count($mediaData) != 3) {
                continue;
            }
            if ($customer->standardAddress()) {
                $codeObj = PostalCodeLatLon::where('postal_code',$customer->standardAddress()->postal_code)->first();
            } else {
                continue;
            }

            $availableUser = $media->product->firm->employees->filter(function ($employee) use ($codeObj) {
                $dist = MessagesHelper::calcDistance($codeObj->latitude, $codeObj->longitude, $employee->latitude, $employee->longitude);
                return $dist < $employee->radius;
            });
            $possibleUsers = $possibleUsers->merge($availableUser);
        }
        $possibleUsers = $possibleUsers->unique('id');
        return $possibleUsers;
    }

    /**
     * @param Collection $possibleUsers
     * @param $chat
     * @param Collection $users
     * @return Collection
     */
    private function filterPossibleUsersWithCurrentlyAdded(Collection $possibleUsers, $chat, Collection $users): Collection
    {
        $possibleUsers = $possibleUsers->filter(function ($item) use ($chat, $users) {
            $filteredEmployeesCount = $chat->chatUsersWithTrashed->filter(function ($user) use ($item) {
                if (empty($user->employee)) {
                    return false;
                }
                return $item->id != $user->employee->id || $item->id == $user->employee->id && $user->trashed();
            })->count();
            return $filteredEmployeesCount == $chat->employees->count();
        });
        return $possibleUsers;
    }

    /**
     * @param $order
     * @param $chat
     * @param Collection $users
     * @return array
     */
    private function getNotAttachedChatUsersForOrder($order, Collection $users): Collection
    {
        $possibleUsers = collect();
        foreach ($order->items as $product) {
            $firm = Firm::where('symbol', 'like', $product->product->product_name_supplier)->first();
            if (empty($firm)) {
                continue;
            }
            $possibleUsers = $possibleUsers->merge($firm->employees);
        }
        return $possibleUsers;
    }

    private function prepareChatView($token)
    {
        $helper = new MessagesHelper($token);
        $chat = $helper->getChat();
        $product = $helper->getProduct();
        $order = $helper->getOrder();
        $helper->setLastRead();
        if (empty($chat)) {
            $users = collect();
            if ($helper->employeeId) {
                $users->push(Employee::find($helper->employeeId));
            }
        } else {
            $users = $chat->chatUsers;
        }
        $possibleUsers = collect();
        $notices = '';
        if ($product && !empty($chat->customers)) {
            $possibleUsers = $this->getNotAttachedChatUsersForProduct($product, $chat->customers->first());
        } else if ($order) {
            $possibleUsers = $this->getNotAttachedChatUsersForOrder($order, $users);
            if ($helper->currentUserType == MessagesHelper::TYPE_USER || $helper->currentUserType == MessagesHelper::TYPE_EMPLOYEE) {
                $notices = $order->consultant_notices;
            }
        }
        $possibleUsers = $this->addCustomerToChatList($chat, $possibleUsers, $users, $helper);
        $productList = $this->prepareProductList($helper);

        $token = $helper->encrypt();
        $view = view('chat.show')->with([
            'product_list' => $productList,
            'faq' => $this->prepareFaq($users),
            'notices' => $notices,
            'possible_users' => $possibleUsers,
            'user_type' => $helper->currentUserType,
            'users' => $users,
            'chat' => $chat,
            'product' => $product,
            'order' => $order,
            'title' => $helper->getTitle(true),
            'route' => route('api.messages.post-new-message', ['token' => $token]),
            'routeAddUser' => route('api.messages.add-new-user', ['token' => $token]),
            'routeRemoveUser' => route('api.messages.remove-user', ['token' => $token]),
            'routeRefresh' => route('api.messages.get-messages', ['token' => $token]),
            'routeAskForIntervention' => route('api.messages.ask-for-intervention', ['token' => $token]),
            'routeForEditPrices' => route('api.messages.edit-prices', ['token' => $token])
        ]);
        return $view;
    }

    private function addCustomerToChatList($chat, $possibleUsers, Collection $users, $helper): Collection
    {
        if ($chat) {
            $possibleUsers = $this->filterPossibleUsersWithCurrentlyAdded($possibleUsers, $chat, $users);
            if ($chat->customers()->whereNull('deleted_at')->count() < 1) {
                if ($helper->getOrder()) {
                    $customer = $helper->getOrder()->customer;
                    $possibleUsers->push($customer);
                }
                if ($helper->getProduct()) {
                    $possibleUsers->push($chat->customers()->first());
                }
            }
        } else {
            if ($helper->getOrder()) {
                $customer = $helper->getOrder()->customer;
                $possibleUsers->push($customer);
            }
        }
        return $possibleUsers;
    }

    private function prepareFaq(Collection $users): array
    {
        $faqs = [];
        foreach ($users as $user) {
            if ($user->employee && $user->employee->faq) {
                $faqs [] = $user->employee->faq;
            }
        }
        return $faqs;
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

    private function prepareProductList(MessagesHelper $helper): Collection
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
        if ($helper->getProduct()) {
            return collect([$helper->getProduct()]);
        }
        return collect();
    }
}
