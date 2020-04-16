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

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($token)
    {
        try {
            $helper = new MessagesHelper($token);
            $chat = $helper->getChat();
            $product = $helper->getProduct();
            $order = $helper->getOrder();
            $helper->setLastRead();
            if (empty($chat)) {
                $users = collect();
            } else {
                $users = $chat->chatUsers;
            }
            $possibleUsers = collect();
            $notices = '';
            if ($product && $chat) {
                $possibleUsers = $this->getNotAttachedChatUsersForProduct($product, $chat, $users);
            } else if ($order && $chat) {
                $possibleUsers = $this->getNotAttachedChatUsersForOrder($order, $chat, $users);
                if ($helper->currentUserType == MessagesHelper::TYPE_USER || $helper->currentUserType == MessagesHelper::TYPE_EMPLOYEE) {
                    $notices = $order->consultant_notices;
                }
            }
            return view('chat.show')->with([
                'notices' => $notices,
                'possible_users' => $possibleUsers,
                'user_type' => $helper->currentUserType,
                'users' => $users,
                'chat' => $chat,
                'product' => $product,
                'order' => $order,
                'title' => $helper->getTitle(true),
                'route' => route('api.messages.post-new-message', ['token' => $helper->encrypt()]),
                'routeAddUser' => route('api.messages.add-new-user', ['token' => $helper->encrypt()]),
                'routeRemoveUser' => route('api.messages.remove-user', ['token' => $helper->encrypt()]),
                'routeRefresh' => route('api.messages.get-messages', ['token' => $helper->encrypt()])
            ]);
        } catch (ChatException $e) {
            $e->log();
            return redirect(env('FRONT_URL'));
        }
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
    private function getNotAttachedChatUsersForProduct($product, $chat, Collection $users): Collection
    {
        $possibleUsers = collect();
        foreach ($product->media()->get() as $media) {
            $mediaData = explode('|', $media->url);
            if (count($mediaData) != 3) {
                continue;
            }
            if ($chat->customers->first()->standardAddress()) {
                $codeObj = PostalCodeLatLon::where('postal_code', $chat->customers->first()->standardAddress()->postal_code)->first();
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

        $possibleUsers = $this->filterPossibleUsersWithCurrentlyAdded($possibleUsers, $chat, $users);
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
    private function getNotAttachedChatUsersForOrder($order, $chat, Collection $users): Collection
    {
        $possibleUsers = collect();
        foreach ($order->items as $product) {
            $firm = Firm::where('symbol', 'like', $product->product->product_name_supplier)->first();
            if (empty($firm)) {
                continue;
            }
            $possibleUsers = $possibleUsers->merge($firm->employees);
        }
        $possibleUsers = $this->filterPossibleUsersWithCurrentlyAdded($possibleUsers, $chat, $users);
        return $possibleUsers;
    }
}
