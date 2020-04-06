<?php

namespace App\Helpers;

use App\Entities\Chat;
use App\Entities\Product;
use App\Entities\Order;
use App\User;
use App\Entities\Customer;
use App\Entities\Employee;
use App\Entities\ProductMedia;
use App\Entities\PostalCodeLatLon;
use App\Entities\Message;
use App\Helpers\Exceptions\ChatException;

class MessagesHelper
{
    public $chatId = 0;
    public $users = [];
    public $productId = 0;
    public $orderId = 0;
    public $employeeId = 0;
    public $currentUserType;
    public $currentUserId;
    private $cache = [];

    const TYPE_CUSTOMER = 'c';
    const TYPE_EMPLOYEE = 'e';
    const TYPE_USER = 'u';

    const SHOW_FRONT = 'f';
    const SHOW_VAR = 'v';
    const NOTIFICATION_TIME = 300;

    const NEW_MESSAGE_LABEL_ID = 55;

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
        if ($this->chatId) {
            return encrypt([
                'cId' => $this->chatId,
                'curT' => $this->currentUserType,
                'curId' => $this->currentUserId
            ]);
        }

        return encrypt([
            'cId' => $this->chatId,
            'u' => $this->users,
            'pId' => $this->productId,
            'oId' => $this->orderId,
            'eId' => $this->employeeId,
            'curT' => $this->currentUserType,
            'curId' => $this->currentUserId
        ]);
    }

    public function decrypt($token)
    {
        $data = decrypt($token);
        $this->chatId = $data['cId'];
        $this->users = $data['u'] ?? [];
        $this->productId = $data['pId'] ?? 0;
        $this->orderId = $data['oId'] ?? 0;
        $this->employeeId = $data['eId'] ?? 0;
        $this->currentUserType = $data['curT'];
        $this->currentUserId = $data['curId'];
        $this->setChatId();
        return $this;
    }

    public function setChatId()
    {
        if ($this->chatId || $this->currentUserType != self::TYPE_CUSTOMER) {
            return;
        }

        if ($this->productId || $this->employeeId) {
            $chat = Chat
                ::where('product_id', $this->productId)
                ->where('employee_id', $this->employeeId)
                ->whereHas('customers', function($q) {
                    $q->where('customers.id', $this->currentUserId);
                })
                ->first()
            ;

            if ($chat) {
                $this->chatId = $chat->id;
            }
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
                    $q->with('user');
                    $q->with('customer');
                    $q->with('employee');
                }]);
                $q->oldest();
            }])
            ->find($this->chatId)
        ;
    }

    public function getTitle()
    {
        $product = $this->getProduct();
        if ($product) {
            return 'Czat dotyczy produktu: '.$product->name.' ('.$product->symbol.')';
        }
        $order = $this->getOrder();
        if ($order) {
            return 'Czat dotyczy zamówienia nr '.$order->id.'.';
        }
        return 'Czat ogólny z administracją '.env('APP_NAME');
    }

    public function createNewChat()
    {
        $chat = new Chat();
        $chat->product_id = $this->productId ?: null;
        $chat->order_id = $this->orderId ?: null;
        $chat->employee_id = $this->employeeId ?: null;
        $chat->save();
        if (empty($this->users[self::TYPE_CUSTOMER])) {
            throw new ChatException('Missing customer ID');
        }
        $customer = Customer::find($this->users[self::TYPE_CUSTOMER]);
        if (!$customer) {
            throw new ChatException('Wrong customer ID');
        }
        $chat->customers()->attach($customer);
        if (!empty($this->users[self::TYPE_EMPLOYEE])) {
            $employee = Employee::find($this->users[self::TYPE_EMPLOYEE]);
            if (!$employee) {
                throw new ChatException('Wrong employee ID');
            }
            $chat->employees()->attach($employee);
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

    public function addMessage($message)
    {
        $chat = $this->getChat();
        $chatUser = $this->getCurrentChatUser();
        if (!$chatUser) {
            throw new ChatException('Cannot save message - User not added to chat');
        }
        $messageObj = new Message();
        $messageObj->message = $message;
        $messageObj->chat_id = $chat->id;
        if (!$chatUser) {
            throw new ChatException('Cannot save message');
        }
        $chatUser->messages()->save($messageObj);
        \App\Jobs\ChatNotificationJob::dispatch($chat->id)->delay(now()->addSeconds(self::NOTIFICATION_TIME + 5));
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

    public static function getToken($mediaId, $postCode, $email)
    {
        $customer = self::getCustomer($email);

        $media = ProductMedia::find($mediaId);

        if (!$media) {
            throw new ChatException('Wrong media ID');
        }

        $mediaData = explode('|', $media->url);

        if (count($mediaData) != 3) {
            throw new ChatException('Media URL corrupted');
        }

        $employee = self::findEmployee($media->product->firm->employees, $mediaData, $postCode);

        $helper = new self();
        $helper->users = [
            self::TYPE_CUSTOMER => $customer->id,
            self::TYPE_EMPLOYEE => $employee->id
        ];
        $helper->productId = $media->product_id;
        $helper->employeeId = $employee->id;
        $helper->currentUserType = self::TYPE_CUSTOMER;
        $helper->currentUserId = $customer->id;

        $helper->setChatId();

        return $helper->encrypt();
    }

    private static function getCustomer($email)
    {
        $customer = Customer::where('login', $email)->first();
        if (!$customer) {
            $customer = new Customer();
            $customer->login = $email;
            $customer->save();
        }
        return $customer;
    }

    private static function calcDistance($lat1, $lon1, $lat2, $lon2)
    {
        return 73 * sqrt(
            pow($lat1 - $lat2, 2) +
            pow($lon1 - $lon2, 2)
        );
    }

    private static function findEmployee($employees, $mediaData, $postCode)
    {
        $foundEmployee = null;
        if ($mediaData[1] == 'c') { //'c' == closest
            $codeObj = PostalCodeLatLon::where('postal_code', $postCode)->first();
            if (!$codeObj) {
                throw new ChatException('Wrong post code');
            }
            $closestDist = 0;
            foreach ($employees as $employee) {
                if (!$employee->employeeRoles()->where('symbol', $mediaData[0])->first()) {
                    continue;
                }
                $dist = self::calcDistance($codeObj->latitude, $codeObj->longitude, $employee->latitude, $employee->longitude);
                if ((!$foundEmployee || $dist < $closestDist) && $dist < $employee->radius) {
                    $foundEmployee = $employee;
                    $closestDist = $dist;
                }
            }
        } else {
            foreach ($employees as $employee) {
                if (!$employee->employeeRoles()->where('symbol', $mediaData[0])->first()) {
                    continue;
                }
                if ($employee->person_number == $mediaData[1]) {
                    $foundEmployee = $employee;
                }
            }
        }
        if (!$foundEmployee) {
            throw new ChatException('Cannot find employee');
        }
        return $foundEmployee;
    }
}
