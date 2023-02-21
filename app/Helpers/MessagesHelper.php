<?php

namespace App\Helpers;

use App\User;
use App\Entities\Chat;
use App\Entities\Label;
use App\Entities\Order;
use App\Enums\UserRole;
use App\Entities\Message;
use App\Entities\Product;
use App\Jobs\AddLabelJob;
use App\Entities\Customer;
use App\Entities\Employee;
use App\Jobs\RemoveLabelJob;
use App\Entities\ProductMedia;
use App\Entities\WorkingEvents;
use App\Entities\CustomerAddress;
use App\Jobs\ChatNotificationJob;
use App\Entities\PostalCodeLatLon;
use Illuminate\Support\Facades\Hash;
use App\Helpers\Exceptions\ChatException;

class MessagesHelper
{
    public $chatId = 0;
    public $users = [];
    public $productId = 0;
    public $orderId = 0;
    public $currentUserType;

    /**
     * User's or Customer's or Employee's id
     **/
    public $currentUserId;
    private $cache = [];

    const TYPE_CUSTOMER = 'c';
    const TYPE_EMPLOYEE = 'e';
    const TYPE_USER = 'u';

    const SHOW_FRONT = 'f';
    const SHOW_VAR = 'v';
    const NOTIFICATION_TIME = 300;

    const MESSAGE_RED_LABEL_ID = 55;
    const MESSAGE_BLUE_LABEL_ID = 56;
    const MESSAGE_YELLOW_LABEL_ID = 57;

    public function __construct($token = null)
    {
        if ($token) {
            $this->decrypt($token);
            if (!$this->getChat() && !$this->getProduct() && !$this->getOrder()) {
                throw new ChatException('Either chat, product or order must exist');
            }
        }
    }

    public function encrypt()
    {
        $this->setChatId();
        $this->setUsers();
        if ($this->chatId) {
            return encrypt([
                'cId' => $this->chatId,
                'curT' => $this->currentUserType,
                'curId' => $this->currentUserId
            ]);
        }

        return encrypt([
            'u' => $this->users,
            'pId' => $this->productId,
            'oId' => $this->orderId,
            'curT' => $this->currentUserType,
            'curId' => $this->currentUserId
        ]);
    }

    public function decrypt($token)
    {
        $data = decrypt($token);
        $this->chatId = $data['cId'] ?? '';
        $this->users = $data['u'] ?? [];
        $this->productId = $data['pId'] ?? 0;
        $this->orderId = $data['oId'] ?? 0;
        $this->currentUserType = $data['curT'];
        $this->currentUserId = $data['curId'];
        $this->setChatId();
        return $this;
    }

    private function setChatId()
    {
        if ($this->chatId) {
            return;
        }

        $q = Chat::query();

        if ($this->orderId) {
            $q->where('order_id', $this->orderId);
        } else {
            $q->whereNull('order_id');
        }

        if ($this->productId) {
            $q->where('product_id', $this->productId);
        } else {
            $q->whereNull('product_id');
        }

        $table = $this->currentUserType == self::TYPE_USER ? 'users' : ($this->currentUserType == self::TYPE_EMPLOYEE ? 'employees' : 'customers');
        $userId = $this->currentUserId;
        $q->whereHas($table, function ($q) use ($table, $userId) {
            $q->where("$table.id", $userId);
        });

        $chat = $q->first();
        if ($chat) {
            $this->chatId = $chat->id;
        }
    }

    private function setUsers()
    {
        $this->users = [];
        if ($this->currentUserType == self::TYPE_CUSTOMER) {
            $this->users[self::TYPE_CUSTOMER] = $this->currentUserId;
        }
        if ($this->currentUserType == self::TYPE_EMPLOYEE) {
            $this->users[self::TYPE_EMPLOYEE] = $this->currentUserId;
        }
        if ($this->currentUserType === self::TYPE_USER) {
            $this->users[self::TYPE_USER] = $this->currentUserId;
        }
    }

    public function getChat()
    {
        if (!array_key_exists('chat', $this->cache)) {
            $this->cache['chat'] = $this->chatId ? $this->getChatObject() : null;
        }
        return $this->cache['chat'];
    }

    public function getProduct()
    {
        if (!array_key_exists('product', $this->cache)) {
            $chat = $this->getChat();
            if ($chat) {
                $this->productId = $chat->product_id;
            }
            $this->cache['product'] = $this->productId ? Product::find($this->productId) : null;
        }
        return $this->cache['product'];
    }

    public function getOrder()
    {
        if (!array_key_exists('order', $this->cache)) {
            $chat = $this->getChat();
            if ($chat) {
                $this->orderId = $chat->order_id;
            }
            $this->cache['order'] = $this->orderId ? Order::find($this->orderId) : null;
        }
        return $this->cache['order'];
    }

    private function getChatObject()
    {
        return Chat
            ::with(['messages' => function($q) {
                $q->with(['chatUser' => function($q) {
                    $q->with(['customer' => function($q) {
                        $q->with(['addresses' => function ($q) {
                            $q->whereNotNull('phone');
                        }]);
                    }]);
                    $q->with('user');
                    $q->with('employee');
                }]);
                $q->oldest();
            }])
            ->with('order')
            ->find($this->chatId)
        ;
    }

    public function getTitle($withBold = false)
    {
        $title = $this->prepareTitleText();
        if (!$withBold) {
            $title = strip_tags($title);
        }
        return $title;
    }

    public function createNewChat()
    {
        $chat = new Chat();
        $this->setUsers();
        $chat->product_id = $this->productId ?: null;
        $chat->order_id = $this->orderId ?: null;
        $chat->save();
        if (!empty($this->users[self::TYPE_CUSTOMER])) {
            $customer = Customer::find($this->users[self::TYPE_CUSTOMER]);
            if (!$customer) {
                throw new ChatException('Wrong customer ID');
            }
            $chat->customers()->attach($customer);
        }
        if (!empty($this->users[self::TYPE_EMPLOYEE])) {
            $employee = Employee::find($this->users[self::TYPE_EMPLOYEE]);
            if (!$employee) {
                throw new ChatException('Wrong employee ID');
            }
            $chat->employees()->attach($employee);
        }
        if (!empty($this->users[self::TYPE_USER])) {
            $user = User::find($this->users[self::TYPE_USER]);
            if (!$user) {
                throw new ChatException('Wrong user ID');
            }
            $chat->users()->attach($user);
        }
        $this->cache['chat'] = $chat;
        $this->chatId = $chat->id;
        return $chat;
    }

    public function canUserSendMessage()
    {
        $chat = $this->getChat();
        switch ($this->currentUserType) {
            case self::TYPE_CUSTOMER:
                return $chat->customers()->where('customers.id', $this->currentUserId)->first() != null;
            case self::TYPE_EMPLOYEE:
                return $chat->employees()->where('employees.id', $this->currentUserId)->first() != null;
            case self::TYPE_USER:
                return $this->getAdminChatUser() != null;
            default:
                return false;
        }
    }

    public function addMessage(string $message, string $area = UserRole::Main): void
    {
        $chat = $this->getChat();
        $chatUser = $this->getCurrentChatUser();
        if (!$chatUser) {
            throw new ChatException('Cannot save message - User not added to chat');
        }
        $messageObj = new Message();
        $messageObj->message = $message;
        $messageObj->chat_id = $chat->id;
        $messageObj->area = $area;
        if (!$chatUser) {
            throw new ChatException('Cannot save message');
        }
        $chatUser->messages()->save($messageObj);
        if ($chat->order) {
            if ($chatUser->user) {
                $this->setChatLabel($chat, true);
                $this->clearIntervention($chat);
            } else {
                $this->setChatLabel($chat, false);
            }
            if ($this->currentUserType == self::TYPE_CUSTOMER) {
                $loopPrevention = [];
                dispatch_now(new AddLabelJob(
                    $chat->order,
                    [self::MESSAGE_YELLOW_LABEL_ID],
                    $loopPrevention,
                    ['added_type' => Label::CHAT_TYPE]
                ));
            } else {
                dispatch_now(new RemoveLabelJob($chat->order, [self::MESSAGE_YELLOW_LABEL_ID]));
            }
            if($this->currentUserType == self::TYPE_USER) {

            }
        }
        WorkingEvents::createEvent(WorkingEvents::CHAT_MESSAGE_ADD_EVENT, $chat->order->id);

        //\App\Jobs\ChatNotificationJob::dispatch($chat->id)->delay(now()->addSeconds(self::NOTIFICATION_TIME + 5));
        // @TODO this should use queue, but at this point (08.05.2021) queue is bugged
        $email = null;

        if ($this->getCurrentChatUser()->customer_id) {
            $email = $this->getCurrentChatUser()->customer->login;
        } else if ($this->getCurrentChatUser()->user_id) {
            $email = $this->getCurrentChatUser()->user->email;
        }

        (new ChatNotificationJob($chat->id, $email))->handle();
    }

    private function getAdminChatUser($secondTry = false)
    {
        $chat = $this->getChat();
        $chatUser = $chat->chatUsers()->whereHas('user', function ($q) {
            $q->where('users.id', $this->currentUserId);
        })->first();
        if ($chatUser) {
            return $chatUser;
        }
        if ($secondTry || !(\Auth::user() instanceof User)) {
            return null;
        }
        $chat->users()->attach(\Auth::user());
        return $this->getAdminChatUser(true);
    }

    private function getCurrentChatUser()
    {
        $column = $this->currentUserType == self::TYPE_CUSTOMER ? 'customer_id' : ($this->currentUserType == self::TYPE_EMPLOYEE ? 'employee_id' : 'user_id');
        if (!$this->getChat()) {
            return null;
        }
        $chatUser = $this->getChat()->chatUsers()->where($column, $this->currentUserId)->first();
        if (!$chatUser && $this->currentUserType == self::TYPE_USER) {
            return $this->getAdminChatUser();
        }
        return $chatUser;
    }

    public function setLastRead()
    {
        $chatUser = $this->getCurrentChatUser();
        if (!$chatUser) {
            return;
        }
        $chatUser->last_read_time = now();
        $chatUser->save();
    }

    public function hasNewMessage()
    {
        return self::hasNewMessageStatic($this->getChat(), $this->getCurrentChatUser());
    }

    public static function hasNewMessageStatic($chat, $chatUser, $notification = false)
    {
        if (!$chatUser) {
            return false;
        }
        for ($i = count($chat->messages) - 1; $i >= 0; $i--) {
            $message = $chat->messages[$i];
            if ($message->created_at > $chatUser->last_read_time && $message->chat_user_id != $chatUser->id) {
                if (!$notification) {
                    return true;
                }
                if (strtotime($chatUser->last_notification_time) + self::NOTIFICATION_TIME < strtotime($message->created_at)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function getToken($mediaId, $postCode, $email, $phone)
    {
        $customer = self::getCustomer($email, $phone, $postCode);

        $media = ProductMedia::find($mediaId);

        if (!$media) {
            throw new ChatException('Wrong media ID');
        }

        $mediaData = explode('|', $media->url);

        if (count($mediaData) != 3) {
            throw new ChatException('Media URL corrupted');
        }

        $helper = new self();
        $helper->productId = $media->product_id;
        $helper->currentUserType = self::TYPE_CUSTOMER;
        $helper->currentUserId = $customer->id;

        return $helper->encrypt();
    }

    private static function getCustomer($email, $phone, $postCode)
    {
        $customer = Customer::where('login', $email)->first();
        if ($customer && $customer->password && !Hash::check($phone, $customer->password)) {
            throw new ChatException('wrong_password');
        }

        if (!$customer) {
            $customer = new Customer();
            $customer->login = $email;
            $customer->save();
            $address = new CustomerAddress();
            $address->type = CustomerAddress::ADDRESS_TYPE_STANDARD;
            $address->phone = $phone;
            $address->email = $email;
            $address->postal_code = $email;
            $customer->addresses()->save($address);

            $customer->save();
        }
        return $customer;
    }

    public static function calcDistance($lat1, $lon1, $lat2, $lon2)
    {
        return 73 * sqrt(
            pow($lat1 - $lat2, 2) +
            pow($lon1 - $lon2, 2)
        );
    }

    private function setChatLabel(Chat $chat, $clearDanger = false)
    {
        if ($clearDanger) {
            $total = Chat::where('order_id', $chat->order->id)->where('need_intervention', true)->count();
            if ($total <= 1 && $chat->need_intervention) {
                $chat->order->labels()->detach(MessagesHelper::MESSAGE_RED_LABEL_ID);
            }
        }
        OrderLabelHelper::setBlueLabel($chat);
    }

    /**
     * @return string
     */
    private function prepareTitleText(): string
    {
        $product = $this->getProduct();
        if ($product) {
            return 'Czat dotyczy produktu: ' . $product->name . ' (<b>' . $product->symbol . '</b>)';
        }
        $order = $this->getOrder();
        if ($order) {
            return 'Czat dotyczy zamówienia nr <b>' . $order->id . '</b>';
        }
        return 'Czat ogólny z administracją ' . env('APP_NAME');
    }

    private function clearIntervention($chat)
    {
        $chat->need_intervention = false;
        $chat->save();
    }

    public function getCurrentUser()
    {
        switch ($this->currentUserType) {
            case self::TYPE_CUSTOMER:
                return Customer::find($this->currentUserId);
            case self::TYPE_EMPLOYEE:
                return Employee::find($this->currentUserId);
            case self::TYPE_USER:
                return User::find($this->currentUserId);
            default:
                throw new \Exception('Userd does not exist');
        }
    }
}
