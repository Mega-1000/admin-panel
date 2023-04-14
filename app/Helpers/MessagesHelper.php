<?php

namespace App\Helpers;

use App\Entities\Chat;
use App\Entities\Customer;
use App\Entities\CustomerAddress;
use App\Entities\Employee;
use App\Entities\Label;
use App\Entities\Message;
use App\Entities\Order;
use App\Entities\Product;
use App\Entities\ProductMedia;
use App\Entities\WorkingEvents;
use App\Enums\UserRole;
use App\Helpers\Exceptions\ChatException;
use App\Http\Controllers\OrdersController;
use App\Http\Requests\NoticesRequest;
use App\Jobs\ChatNotificationJob;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Entities\ChatUser;

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
    const MESSAGE_GREEN_LABEL_ID = 58;

    public function __construct($token = null)
    {
        if ($token) {
            $this->decrypt($token);
            if (!$this->getChat() && !$this->getProduct() && !$this->getOrder()) {
                throw new ChatException('Either chat, product or order must exist');
            }
        }
    }

    /**
     * Encrypt data
     *
     * @return string
     */
    public function encrypt(): string
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

        $dataToEncrypt = [
            'u' => $this->users,
            'curT' => $this->currentUserType,
            'curId' => $this->currentUserId
        ];
        if($this->orderId) {
            $dataToEncrypt['oId'] = $this->orderId;
        }
        if($this->productId) {
            $dataToEncrypt['pId'] = $this->productId;
        }

        return encrypt($dataToEncrypt);
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
        return Chat::with(['messages' => function ($q) {
                $q->with(['chatUser' => function ($q) {
                    $q->with(['customer' => function ($q) {
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
            ->find($this->chatId);
    }

    public function getTitle($withBold = false)
    {
        $title = $this->prepareTitleText();
        if (!$withBold) {
            $title = strip_tags($title);
        }
        return $title;
    }

    /**
     * Get or if no exist create Blank user (user without any ids, for sending generic messages)
     *
     * @param  Chat     $chat
     *
     * @return ChatUser $chatUser
     */
    public function createOrGetBlankUser(Chat $chat): ChatUser {
        $chatUser = $chat->chatUsers->whereNull('user_id')->whereNull('customer_id')->whereNull('employee_id')->first();

        if($chatUser === null) {

            $chatUser = new ChatUser();
            $chatUser->chat()->associate($chat);
            $chatUser->save();
        }

        return $chatUser;
    }

    public function createNewChat()
    {
        $chat = new Chat();
        $chat->order_id = null;

        if ($this->orderId) {
            // ensure that is only one order chat
            $chat = Chat::where('order_id', $this->orderId)->first();
            $this->setUsers();

            if ($chat) {
                $this->cache['chat'] = $chat;
                $chat->order_id = $this->orderId;
                return $chat;
            }

            $chat = new Chat();
            $chat->order_id = $this->orderId;
        }

        $chat->product_id = $this->productId ?: null;

        $this->cache['chat'] = $chat;
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

    /**
     * Handle add message to Chat
     *
     * @param string             $message
     * @param string             $area
     * @param UploadedFile|null  $file
     * @param ChatUser|null      $customChatUser
     *
     * @return Message      $msg
     */
    public function addMessage(string $message, int $area = UserRole::Main, UploadedFile $file = null, ?ChatUser $customChatUser = null): Message
    {
        $chat = $this->getChat();
        $chatUser = $customChatUser ?? $this->getCurrentChatUser();
        if (!$chatUser) {
            throw new ChatException('Cannot save message - User not added to chat');
        }

        $messageObj = new Message();
        $messageObj->message = $message;
        $messageObj->chat_id = $chat->id;
        $messageObj->area = $area;
        if ($area != 0 && $this->currentUserType != self::TYPE_USER) {
            throw new ChatException('You don\'t have permission to write in other area');
        } else if ($area != 0 && $this->currentUserType == self::TYPE_USER && $chat->order_id) {

            // map UserRole Enum to Order constants
            $type = [
                '1' => Order::COMMENT_SHIPPING_TYPE,
                '2' => Order::COMMENT_SHIPPING_TYPE,
                '3' => Order::COMMENT_FINANCIAL_TYPE,
                '4' => Order::COMMENT_CONSULTANT_TYPE,
                '5' => Order::COMMENT_WAREHOUSE_TYPE,
            ];
            $noticesRequestParams = [
                'message' => $message,
                'type' => $type[$area],
                'order_id' => $chat->order_id,
                'user_id' => $chatUser->user_id,
            ];
            $noticeRequest = new NoticesRequest($noticesRequestParams);
            $noticeValidator = Validator::make($noticeRequest->all(), $noticeRequest->rules());
            $noticeRequest->setValidator($noticeValidator);

            $orderController = app(OrdersController::class);
            $orderController->updateNotices($noticeRequest);
        }
        if ($file) {
            $originalFileName = $file->getClientOriginalName();
            $hashedFileName = Hash::make($originalFileName);
            $path = $file->storeAs('chat_files/' . $chat->id, $hashedFileName, 'public');
            if ($path) {
                $messageObj->attachment_path = $path;
                $messageObj->attachment_name = $originalFileName;
            }
        }
        if (!$chatUser) {
            throw new ChatException('Cannot save message');
        }
        $msg = $chatUser->messages()->save($messageObj);

        // assign messages if area is default (0)
        if ($area == 0) {
            foreach ($chat->chatUsers as $singleUser) {
                if ($singleUser->user_id !== null) continue;

                $assignedMessagesIds = json_decode($singleUser->assigned_messages_ids ?: '[]', true);
                $assignedMessagesIds[] = $msg->id;
                $singleUser->assigned_messages_ids = json_encode($assignedMessagesIds);
                $singleUser->save();
            }
        }

        if ($chat->order) {
            if($this->currentUserType === self::TYPE_CUSTOMER && $chat->user_id === null) {
                $chat->order->need_support = true;
                $chat->order->save();
            }
            if ($chatUser->user) {
                $this->setChatLabel($chat, true);
            } else {
                $this->setChatLabel($chat, false);
            }
            if ($this->currentUserType == self::TYPE_CUSTOMER) {
                $loopPrevention = [];
                AddLabelService::addLabels(
                    $chat->order,
                    [self::MESSAGE_YELLOW_LABEL_ID],
                    $loopPrevention,
                    ['added_type' => Label::CHAT_TYPE],
                    Auth::user()?->id
                );
            } else if (isset(Auth::user()->id)) {
                $loopPrevention = [];
                RemoveLabelService::removeLabels(
                    $chat->order, [self::MESSAGE_YELLOW_LABEL_ID],
                    $loopPrevention,
                    [],
                    Auth::user()->id
                );
            }
            WorkingEvents::createEvent(WorkingEvents::CHAT_MESSAGE_ADD_EVENT, $chat->order->id);
        } else {
            if($this->currentUserType === self::TYPE_CUSTOMER && $chat->user_id === null) {
                $chat->need_intervention = true;
                $chat->save();
            }
        }

        $email = null;

        if ($this->getCurrentChatUser()->customer_id) {
            $email = $this->getCurrentChatUser()->customer->login;
        } else if ($this->getCurrentChatUser()->user_id) {
            $email = $this->getCurrentChatUser()->user->email;
        }

        (new ChatNotificationJob($chat->id, $email, $this->getCurrentChatUser()->id))->handle();

        return $msg;
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
        if ($secondTry || !(Auth::user() instanceof User)) {
            return null;
        }
        $chat->users()->attach(Auth::user());
        return $this->getAdminChatUser(true);
    }

    public function getCurrentChatUser()
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

    /**
     * Get encrypted chat token for given data
     *
     * @param  int      $orderId
     * @param  int      $userId
     * @param  string   $userType
     *
     * @return string   $chatUserToken
     */
    public function getChatToken(?int $orderId, int $userId, string $userType = MessagesHelper::TYPE_USER): string {
        $this->orderId = $orderId;
        $this->currentUserId = $userId;
        $this->currentUserType = $userType;
        $chatUserToken = $this->encrypt();

        return $chatUserToken;
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

    public static function getToken(int $mediaId, string $postCode, string $email, string $phone): string
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

    private static function getCustomer(string $email, string $phone, string $postCode): Customer
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
            $address->postal_code = $postCode;
            $customer->addresses()->save($address);

            $customer->save();
        }
        return $customer;
    }

    public static function calcDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        return 73 * sqrt(
                pow($lat1 - $lat2, 2) +
                pow($lon1 - $lon2, 2)
            );
    }

    private function setChatLabel(Chat $chat, bool $clearDanger = false): void
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
        return 'Czat ogólny z administracją mega1000';
    }

    private function clearIntervention($chat)
    {
        $chat->need_intervention = false;
        $chat->save();
    }

    /**
     * @throws Exception
     */
    public function getCurrentUser()
    {
        return match ($this->currentUserType) {
            self::TYPE_CUSTOMER => Customer::find($this->currentUserId),
            self::TYPE_EMPLOYEE => Employee::find($this->currentUserId),
            self::TYPE_USER => User::find($this->currentUserId),
            default => throw new Exception('Userd does not exist'),
        };
    }

    /**
     * Prepare list of products
     *
     * @return Collection<OrderItem|null>
     */
    public function prepareOrderItemsCollection(): Collection
    {
        try {
            $chatUser = $this->getCurrentUser();
            $order = $this->getOrder();

            if (is_a($chatUser, Employee::class)) {
                return $order->items->filter(function ($item) use ($chatUser) {
                    return empty($item->product->firm) || $item->product->firm->id == $chatUser->firm->id;
                });
            }
            return $order->items;
        } catch (Exception $e) {
            Log::error('Cannot prepare product list',
                ['exception' => $e->getMessage(), 'class' => $e->getFile(), 'line' => $e->getLine()]);
            return collect();
        }
    }

    /**
     * Prepare Employees for possible Users
     *
     * @param Collection $employeesIds
     * @param Collection $currentEmployeesOnChat - collections with Employees ids
     * @return Collection<Employee>
     */
    public function prepareEmployees(Collection $employeesIds, Collection $currentEmployeesOnChat): Collection
    {

        $employeesIdsFiltered = [];
        foreach ($employeesIds as $productEmployees) {

            if (is_string($productEmployees)) {
                $productEmployees = json_decode($productEmployees ?: '[]', true);
            }

            if (empty($productEmployees)) continue;

            foreach ($productEmployees as $employeeId) {
                $employeesIdsFiltered[] = $employeeId;
            }
        }

        // remove no unique employees
        $employeesIdsFiltered = collect($employeesIdsFiltered)->unique();
        // remove already existed as chat users employees
        $employeesIdsFiltered = $employeesIdsFiltered->diff($currentEmployeesOnChat);

        $possibleUsers = Employee::findMany($employeesIdsFiltered);

        return $possibleUsers;
    }

    /**
     * Send complaint email to employee with given chat token
     *
     * @param  string $email
     *
     * @return void
     */
    public function sendComplaintEmail(string $email) {

        $chat = $this->getChat();
        $complaintForm = $chat->complaint_form;

        if($chat === null) {
            throw new ChatException('Nieprawidłowy token chatu');
        }
        if($complaintForm === '') {
            throw new ChatException('Czat nie posiada uzupełnionego formularza reklamacji');
        }
        if($this->currentUserType !== self::TYPE_USER) {
            throw new ChatException('Nie masz uprawnień do wysłania wiadomości');
        }

        $employee = Employee::where('email', $email)->first();
        
        if($employee === null) {
            throw new ChatException('Brak pracownika dla danego adresu Email');
        }

        $chatUser = ChatUser::where([
            'chat_id' => $chat->id,
            'employee_id' => $employee->id,
        ])->first();

        if($chatUser === null) {
            $chatUser = new ChatUser();
            $chatUser->chat()->associate($chat);
            $chatUser->employee()->associate($employee);
            $chatUser->save();
        }

        $newChatToken = $this->getChatToken($chat->order_id, $employee->id, self::TYPE_EMPLOYEE);

        $subject = 'Reklamacja do oferty EPH ID ' . $chat->order_id;

        $complaintForm = json_decode($complaintForm);

        if( isset($complaintForm->trackingNumber) ) {
            $subject .= ', numer listu przewozowego: '.$complaintForm->trackingNumber;
        }
        Helper::sendEmail(
            $email,
            'chat-complaint-form',
            $subject,
            [
                'url' => route('chat.show', ['token' => $newChatToken]),
                'title' => $this->getTitle(false),
                'complaintForm' => $complaintForm,
            ]
        );
    }
}
