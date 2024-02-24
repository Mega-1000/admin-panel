<?php

namespace App\Helpers;

use App\ChatStatus;
use App\Entities\Chat;
use App\Entities\ChatUser;
use App\Entities\Customer;
use App\Entities\CustomerAddress;
use App\Entities\Employee;
use App\Entities\Label;
use App\Entities\Message;
use App\Entities\Order;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Entities\ProductMedia;
use App\Entities\WorkingEvents;
use App\Enums\UserRole;
use App\Helpers\Exceptions\ChatException;
use App\Http\Controllers\OrdersController;
use App\Http\Requests\NoticesRequest;
use App\Jobs\ChatNotificationJob;
use App\Repositories\Chats;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\WorkingEventsService;
use App\User;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MessagesHelper
{
    public mixed $chatId = 0;
    public array $users = [];
    public mixed $productId = 0;
    public ?int $orderId = 0;
    public $currentUserType;

    /**
     * User's or Customer's or Employee's id
     **/
    public $currentUserId = null;
    private array $cache = [];

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

    /**
     * @throws ChatException
     */
    public function __construct($token = null)
    {
        if ($token) {
            $this->decrypt($token);
            if (!$this->getChat() && !$this->getProduct() && !$this->getOrder()) {
                throw new Exception('Either chat, product or order must exist');
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

    public function decrypt($token): static
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

    private function setChatId(): void
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

    private function setUsers(): void
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

    private function getChatObject(): Chat
    {
        return Chats::getFullChatObject($this->chatId);
    }

    public function getTitle($withBold = false): string
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
     * @param Chat $chat
     *
     * @return ChatUser|null $chatUser
     */
    public function createOrGetBlankUser(Chat $chat): mixed
    {
        $chatUser = $chat->chatUsers->whereNull('user_id')->whereNull('customer_id')->whereNull('employee_id')->first();

        if ($chatUser === null) {
            $chatUser = new ChatUser();
            $chatUser->chat()->associate($chat);
            $chatUser->save();
        }

        return $chatUser;
    }

    /**
     * @throws ChatException
     */
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

        $blankChatUser = $this->createOrGetBlankUser($chat);

//        $chatStatus = ChatStatus::first();
//        $content = $chatStatus->is_active ? $chatStatus->message : "Witamy! W czym możemy pomóc?";
//        $this->addMessage($content, UserRole::Main, null, $blankChatUser);

        $this->cache['chat'] = $chat;
        $this->chatId = $chat->id;
        return $chat;
    }

    public function canUserSendMessage(): bool
    {
        $chat = $this->getChat();
        return match ($this->currentUserType) {
            self::TYPE_CUSTOMER => $chat->customers()->where('customers.id', $this->currentUserId)->first() != null,
            self::TYPE_EMPLOYEE => $chat->employees()->where('employees.id', $this->currentUserId)->first() != null,
            self::TYPE_USER => $this->getAdminChatUser() != null,
            default => false,
        };
    }

    /**
     * Handle add message to Chat
     *
     * @param string $message
     * @param int $area
     * @param UploadedFile|null $file
     * @param ChatUser|null $customChatUser
     * @param mixed|null $chat
     *
     * @return Message      $msg
     * @throws ChatException
     */
    public function addMessage(string $message, int $area = UserRole::Main, UploadedFile $file = null, ?ChatUser $customChatUser = null, mixed $chat = null): Message
    {
        $chat = $chat ?? $this->getChat();
        $chatUser = $customChatUser ?? $this->getCurrentChatUser() ?? $this->createOrGetBlankUser($chat);
        if (!$chatUser) {
            throw new ChatException('Cannot save message - User not added to chat');
        }

        $messageObj = new Message();
        $messageObj->message = $message;
        $messageObj->chat_id = $chat->id;
        $messageObj->area = $area;
        if ($area != 0 && $this->currentUserType != self::TYPE_USER) {
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
            if($this->currentUserType === self::TYPE_CUSTOMER && $chat->user_id === null && $chatUser->customer_id !== null) {
                $chat->order->need_support = true;
                $this->sendWaitingMessage($chat);
                $chat->order->save();
            }
            if ($chatUser->user) {
                $this->setChatLabel($chat, true, $area);
            } else {
                $this->setChatLabel($chat, false, $area);
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
            } else if (isset(Auth::user()->id) && $area == 0) {
                $loopPrevention = [];
                RemoveLabelService::removeLabels(
                    $chat->order, [self::MESSAGE_YELLOW_LABEL_ID],
                    $loopPrevention,
                    [],
                    Auth::user()->id
                );
            }
            WorkingEventsService::createEvent(WorkingEvents::CHAT_MESSAGE_ADD_EVENT, $chat->order->id);
        } else {
            if($this->currentUserType === self::TYPE_CUSTOMER && $chat->user_id === null && $chatUser->customer_id !== null) {

                $chat->need_intervention = true;
                $this->sendWaitingMessage($chat);
                $chat->save();
            }
        }

        $email = null;

        if ($chatUser->customer_id) {
            $email = $chatUser->customer->login;
        } else if ($chatUser->user_id) {
            $email = $chatUser->user->email;
        }

        (new ChatNotificationJob($chat->id, $email, $chatUser->id))->handle();

        return $msg;
    }

    /**
     * Send waiting message on chat
     *
     * @param Chat $chat
     *
     * @return void
     * @throws ChatException
     */
    private function sendWaitingMessage(Chat $chat): void
    {
//        $content = "Konsultant zapoznaje się ze sprawą wkrótce się odezwie.
//                    Zajmuje to zwykle do kilku minut.";
//        $blankChatUser = $this->createOrGetBlankUser($chat);
//
//        $this->addMessage($content, UserRole::Main, null, $blankChatUser);
    }

    public function sendDateChangeMessage(Chat $chat, string $type): void
    {
        $content = "Zmieniono daty dostawy, zmieniający: " . $type . ". Prosimy o zapoznanie się z nowymi terminami i zatwierdzenie.";
        $blankChatUser = $this->createOrGetBlankUser($chat);
        $this->chatId = $chat->id;

        $this->addMessage($content, UserRole::Main, null, $blankChatUser);
    }

    public function sendDateAcceptationMessage(Chat $chat): void
    {
        $content = 'Daty zostały finalnie zatwierdzone.';
        $blankChatUser = $this->createOrGetBlankUser($chat);
        $this->chatId = $chat->id;

        $this->addMessage($content, UserRole::Main, null, $blankChatUser);
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

        if (!$chatUser && $this->currentUserId == self::TYPE_USER) {
            return $this->getAdminChatUser();
        }

        return $chatUser ?? $this->getChat()->whereHas('chatUsers', function ($query) {
            $query->whereHas('employee');
        })->first()->chatUsers()->first();
    }

    public function setLastRead(): void
    {
        $chatUser = $this->getCurrentChatUser();

        if (!$chatUser) {
            return;
        }

        $chatUser->last_read_time = now();
        $chatUser->save();
    }

    public function hasNewMessage(): bool
    {
        return self::hasNewMessageStatic($this->getChat(), $this->getCurrentChatUser());
    }

    /**
     * Get encrypted chat token for given data
     *
     * @param int|null $orderId
     * @param int $userId
     * @param string $userType
     *
     * @return string   $chatUserToken
     */
    public function getChatToken(?int $orderId, int $userId, string $userType = MessagesHelper::TYPE_USER): string {
        $this->orderId = $orderId;
        $this->currentUserId = $userId;
        $this->currentUserType = $userType;

        return $this->encrypt();
    }

    public static function hasNewMessageStatic($chat, $chatUser, $notification = false): bool
    {
        if (!$chatUser) {
            return false;
        }

        $chat->messages = $chat->messages->sortByDesc('created_at');

        foreach ($chat->messages as $message) {
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

    /**
     * @throws ChatException
     */
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

    /**
     * @throws ChatException
     */
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

    private function setChatLabel(Chat $chat, bool $clearDanger = false, int $area = 0): void
    {
        if ($clearDanger) {
            $total = Chat::where('order_id', $chat->order->id)->where('need_intervention', true)->count();
            if ($total <= 1 && $chat->need_intervention) {
                $chat->order->labels()->detach(MessagesHelper::MESSAGE_RED_LABEL_ID);
            }
        }

        if($area == 0) {
            OrderLabelHelper::setBlueLabel($chat);
        }
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

        return 'Chat ogólny z administracją EPH POLSKA';
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

        return Employee::findMany($employeesIdsFiltered);
    }

    /**
     * Send complaint email to employee with given chat token
     *
     * @param string $email
     *
     * @return void
     * @throws ChatException
     */
    public function sendComplaintEmail(string $email): void
    {

        $chat = $this->getChat();
        $complaintForm = $chat->complaint_form;

        if ($chat === null) {
            throw new ChatException('Nieprawidłowy token chatu');
        }
        if ($complaintForm === '') {
            throw new ChatException('Czat nie posiada uzupełnionego formularza reklamacji');
        }
        if ($this->currentUserType !== self::TYPE_USER) {
            throw new ChatException('Nie masz uprawnień do wysłania wiadomości');
        }

        $employee = Employee::where('email', $email)->first();

        if ($employee === null) {
            throw new ChatException('Brak pracownika dla danego adresu Email');
        }

        $chatUser = ChatUser::where([
            'chat_id' => $chat->id,
            'employee_id' => $employee->id,
        ])->first();

        if ($chatUser === null) {
            $chatUser = new ChatUser();
            $chatUser->chat()->associate($chat);
            $chatUser->employee()->associate($employee);
            $chatUser->save();
        }

        $newChatToken = $this->getChatToken($chat->order_id, $employee->id, self::TYPE_EMPLOYEE);

        $subject = 'Reklamacja do oferty EPH ID ' . $chat->order_id;

        $complaintForm = json_decode($complaintForm);

        if (isset($complaintForm->trackingNumber)) {
            $subject .= ', numer listu przewozowego: '.$complaintForm->trackingNumber;
        }
        Helper::sendEmail(
            $email,
            'chat-complaint-form',
            $subject,
            [
                'url' => route('chat.show', ['token' => $newChatToken]),
                'title' => $this->getTitle(),
                'complaintForm' => $complaintForm,
            ]
        );
    }

    public static function sendAsCurrentUser(Order $order, string $message, int $chatArea = UserRole::Consultant) {
        $messagesHelper = new self();

        $chat = $order->chat()->firstOrCreate();
        $chatUser = $chat->chatUsers()->firstOrCreate(['user_id' => Auth::user()?->id]);
        $messagesHelper->currentUserType = MessagesHelper::TYPE_USER;
        $messagesHelper->addMessage($message, $chatArea, null, $chatUser, $chat);
    }
}
