<?php

namespace App\Helpers;

use App\Entities\Chat;
use App\Entities\Product;
use App\Entities\Order;
use App\Entities\Customer;
use App\Entities\ProductMedia;
use App\Entities\PostalCodeLatLon;
use App\Entities\Message;

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

    public function __construct($token = null)
    {
        if ($token) {
            $this->decrypt($token);
            if (!$this->getChat() && !$this->getProduct() && !$this->getOrder()) {
                throw new \Exception('Either chat, product or order must exist');
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
        if ($this->currentUserType != 'c') {
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
            $this->cache['product'] = $this->productId ? Product::find($this->productId) : null;
        }
        return $this->cache['product'];
    }

    public function getOrder()
    {
        if (!array_key_exists('order', $this->cache)) {
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
        $chat->product_id = $this->productId;
        $chat->order_id = $this->orderId;
        $chat->employee_id = $this->employeeId;
        $chat->save();
        if (empty($this->users['c'])) {
            throw new \Exception('Missing customer ID');
        }
        $customer = \App\Entities\Customer::find($this->users['c']);
        if (!$customer) {
            throw new \Exception('Wrong customer ID');
        }
        $chat->customers()->attach($customer);
        if (!empty($this->users['e'])) {
            $employee = \App\Entities\Employee::find($this->users['e']);
            if (!$employee) {
                throw new \Exception('Wrong employee ID');
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
            case 'c':
                return $chat->customers()->where('customers.id', $this->currentUserId)->first() != null;
            case 'e':
                return $chat->employees()->where('employees.id', $this->currentUserId)->first() != null;
            case 'u':
                if (!$chat->users()->where('users.id', $this->currentUserId)->first()) {
                    if (!(\Auth::user() instanceof \App\Entities\User)) {
                        return false;
                    }
                    $chat->users()->attach(\Auth::user());
                }
            default:
                return false;
        }
    }

    public function addMessage($message)
    {
        $chat = $this->getChat();
        $column = $this->currentUserType == 'c' ? 'customer_id' : ($this->currentUserType == 'e' ? 'employee_id' : 'user_id');
        $messageObj = new Message();
        $messageObj->message = $message;
        $messageObj->chat_id = $chat->id;
        $chatUser = $chat->chatUser()->where($column, $this->currentUserId)->first();
        if (!$chatUser) {
            throw new \Exception('Cannot save message');
        }
        $chatUser->messages()->save($messageObj);
    }

    public static function getToken($mediaId, $postCode, $email)
    {
        $customer = self::getCustomer($email);

        $media = ProductMedia::find($mediaId);

        if (!$media) {
            throw new \Exception('Wrong media ID');
        }

        $mediaData = explode('|', $media->url);

        if (count($mediaData) != 3) {
            throw new \Exception('Media URL corrupted');
        }

        $employee = self::findEmployee($media->product->warehouse->employees, $mediaData, $postCode);

        $helper = new self();
        $helper->users = [
            'c' => $customer->id,
            'e' => $employee->id
        ];
        $helper->productId = $media->product_id;
        $helper->employeeId = $employee->id;
        $helper->currentUserType = 'c';
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
        if ($mediaData[1] == 'c') {
            $codeObj = PostalCodeLatLon::where('postal_code', $postCode)->first();
            if (!$codeObj) {
                throw new \Exception('Wrong post code');
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
            throw new \Exception('Cannot find employee');
        }
        return $foundEmployee;
    }
}