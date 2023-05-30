<?php
declare(strict_types=1);

namespace App\Helpers;

use App\Entities\CustomerAddress;
use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\interfaces\iDividable;
use App\Helpers\interfaces\iGetUser;
use App\Helpers\interfaces\iOrderPriceOverrider;
use App\Helpers\interfaces\iOrderTotalPriceCalculator;
use App\Helpers\interfaces\iPostOrderAction;
use App\Helpers\interfaces\iSumable;
use App\Services\ProductService;
use App\Services\EmailSendingService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Entities\Customer;

class OrderBuilder
{

    const VALID_EXTENSIONS = ['png', 'jpg', 'jpeg', 'pdf', 'tif', 'gif'];

    /**
     * @var iDividable
     */
    private iDividable $packageGenerator;

    /**
     * @var iOrderTotalPriceCalculator
     */
    private iOrderTotalPriceCalculator $priceCalculator;

    /**
     * @var iOrderPriceOverrider
     */
    private $priceOverrider;

    /**
     * @var iSumable
     */
    private iSumable $totalTransportSumCalculator;

    /**
     * @var iGetUser
     */
    private iGetUser $userSelector;

    /**
     * @var iPostOrderAction
     */
    private $postOrderActions;

    /**
     * @var ProductService
     */
    private ProductService $productService;


    public function setPackageGenerator(iDividable $generator): static
    {
        $this->packageGenerator = $generator;
        return $this;
    }

    public function setPriceCalculator(iOrderTotalPriceCalculator $priceCalculator): static
    {
        $this->priceCalculator = $priceCalculator;
        return $this;
    }

    public function setPriceOverrider(iOrderPriceOverrider $priceOverrider): static
    {
        $this->priceOverrider = $priceOverrider;
        return $this;
    }

    public function setTotalTransportSumCalculator(iSumable $calculator): static
    {
        $this->totalTransportSumCalculator = $calculator;
        return $this;
    }

    public function setUserSelector(iGetUser $userSelector): static
    {
        $this->userSelector = $userSelector;
        return $this;
    }

    public function setPostOrderActions(iPostOrderAction $postOrderActions): static
    {
        $this->postOrderActions = $postOrderActions;
        return $this;
    }

    public function setProductService(ProductService $productService): OrderBuilder
    {
        $this->productService = $productService;
        return $this;
    }

    /**
     * Handle store new order
     *
     * @param  array         $data
     * @param  Customer|null $customer
     *
     * @throws ChatException
     * @throws Exception
     * @return array
     */
    public function newStore(array $data, ?Customer $customer): array
    {
        if (empty($this->packageGenerator) || empty($this->priceCalculator) || empty($this->userSelector)) {
            throw new Exception('Nie zdefiniowano bazowych komponentów klasy');
        }
        OrderBuilder::setEmptyOrderData($data);
        if (empty($data['order_items']) || !is_array($data['order_items'])) {
            throw new Exception('missing_products');
        }

        $orderExists = false;
        if (!empty($data['cart_token'])) {
            $order = Order::where('token', $data['cart_token'])->first();
            $order->clearPackages();
            $orderExists = true;
        } else {
            $order = new Order();
        }

        $order->getToken();
        if($customer === null) {
            $customer = $this->userSelector->getCustomer($order, $data);
        }
        $order->customer_id = $customer->id;

        if (!$orderExists) {
            $order->status_id = 1;
            OrderBuilder::assignEmployeeToOrder($order, $customer);
        }

        if (!empty($data['shipping_abroad'])) {
            $order->shipping_abroad = 1;
        }

        $order->save();

        $emailSendingService = new EmailSendingService();
        $emailSendingService->addNewScheduledEmail($order);

        if (!empty($data['files'])) {
            foreach ($data['files'] as $file) {
                $this->attachFileToOrder($file, $order);
            }
        }
        $chatUserToken = '';
        if ( isset($data['need_support']) && $data['need_support'] === true ) {
            $helper = new MessagesHelper();
            $chatUserToken = $helper->getChatToken($order->id, $customer->id, MessagesHelper::TYPE_CUSTOMER);
            $helper->createNewChat();

            if( !empty($data['customer_notices']) ) {
                $helper->addMessage($data['customer_notices']);
            }
            $order->labels()->attach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
            $order->need_support = true;
        }
        $this->assignItemsToOrder($order, $data['order_items']);

        $deliveryEmail = isset($data['delivery_address']) ? $data['delivery_address']['email'] ?? '' : '';
        $invoiceEmail = isset($data['invoice_address']) ? $data['invoice_address']['email'] ?? '' : '';
        OrderBuilder::updateOrderAddress($order, $data['delivery_address'] ?? [], 'DELIVERY_ADDRESS', $data['phone'] ?? '', 'order', $deliveryEmail);
        OrderBuilder::updateOrderAddress($order, $data['invoice_address'] ?? [], 'INVOICE_ADDRESS', $data['phone'] ?? '', 'order', $invoiceEmail);
        if (isset($data['is_standard']) && $data['is_standard'] || $order->customer->addresses()->count() < 2) {
            OrderBuilder::updateOrderAddress(
                $order,
                $data['delivery_address'] ?? [],
                CustomerAddress::ADDRESS_TYPE_STANDARD,
                $data['phone'] ?? '',
                'customer',
                $data['customer_login'] ?? '',
                $data['update_email'],
                $data['update_customer'] ?? false
            );
            OrderBuilder::updateOrderAddress(
                $order,
                $data['delivery_address'] ?? [],
                'DELIVERY_ADDRESS',
                $data['phone'] ?? '',
                'customer',
                $data['customer_login'] ?? '',
                $data['update_email']
            );
        }

        try {
            $canPay = $this->packageGenerator->divide($data['order_items'], $order);
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            Log::error("Problem with package dividing: $message", ['class' => $exception->getFile(), 'line' => $exception->getLine()]);
        }
        if ($this->totalTransportSumCalculator) {
            $order->shipment_price_for_client = $this->totalTransportSumCalculator->getSum($order);
        }
        $order->save();
        $this->postOrderActions?->run($order);
        return ['id' => $order->id, 'canPay' => $canPay ?? false, 'chatUserToken' => $chatUserToken];
    }

    /**
     * @throws Exception
     */
    private static function setEmptyOrderData(&$data): void
    {
        if (!empty($data['want_contact'])) {
            $data = [
                'phone' => $data['phone'],
                'customer_login' => $data['phone'] . '@mega1000.pl',
                'customer_notices' => '',
                'delivery_address' => [
                    'city' => 'Oława',
                    'postal_code' => '55-200'
                ],
                'is_standard' => false,
                'rewrite' => 0
            ];
        }

        $data['update_email'] = false;
        if (empty($data['order_items'])) {
            $data['order_items'] = OrderBuilder::getDefaultProduct();
            $data['update_email'] = true;
        }
    }

    /**
     * @throws Exception
     */
    private static function getDefaultProduct(): array
    {
        $product = Product::getDefaultProduct();

        return [['id' => $product->id, 'amount' => 1]];
    }

    private static function assignEmployeeToOrder($order, $customer): void
    {
        $orderCustomerOpenExists = Order::where('customer_id', $customer->id)
            ->whereNotIn('status_id', [6, 8])
            ->whereNotNull('employee_id')
            ->orderBy('id', 'desc')
            ->first();

        if (!empty($orderCustomerOpenExists)) {
            $order->employee_id = $orderCustomerOpenExists->employee_id;
        }
    }

    /**
     * @param $file
     * @param Order $order
     */
    protected function attachFileToOrder($file, Order $order): void
    {
        $data = explode(',', $file['base64'])[1];
        $fileDecoded = base64_decode($data);
        $extension = explode('.', $file['name']);
        $extension = end($extension);
        if (!in_array($extension, self::VALID_EXTENSIONS)) {
            return;
        }
        $random = Str::random(40);
        Storage::disk('private')->put('files/' . $order->id . '/' . $random . '.' . $extension, $fileDecoded);
        $order->files()->create([
            'file_name' => $file['name'],
            'hash' => $random . '.' . $extension
        ]);
    }

    /**
     * @throws Exception
     */
    public function assignItemsToOrder($order, $items): void
    {
        $weight = 0;
        $orderItems = $order->items;
        $order->items()->delete();
        $oldPrices = [];

        foreach ($orderItems as $item) {
            foreach (OrderBuilder::getPriceColumns() as $column) {
                $oldPrices[$item->product_id][$column] = $item->$column;
            }
        }

        foreach ($items as $item) {
            $product = Product::find($item['id']);
            if (empty($product)) {
                throw new Exception('product_not_found');
            }

            $price = $product->price;
            if (!$price) {
                throw new Exception('wrong_product_id');
            }

            $getStockProduct = $this->productService->getStockProduct($product->id);

            $orderItem = new OrderItem();
            $orderItem->quantity = $item['amount'];

            if (!empty($item['type'])) {
                $orderItem->type = $item['type'];
            }

            $orderItem->product_id = $getStockProduct ? $getStockProduct->id : $product->id;
            Log::info('Bazowe id produktu: ' . $product->id . ' oraz symbol' . $product->symbol . '. Wynikowe id produktu: ' . $orderItem->product_id);
            foreach (OrderBuilder::getPriceColumns() as $column) {
                if (empty($item['recalculate']) && isset($oldPrices[$product->id])) {
                    $orderItem->$column = $oldPrices[$product->id][$column];
                } else {
                    if ($column === "gross_selling_price_commercial_unit") {
                        $orderItem->$column = !empty($item['gross_selling_price_commercial_unit']) ? $item['gross_selling_price_commercial_unit'] : $price->gross_price_of_packing;
                    } else {
                        $orderItem->$column = $price->$column;
                    }
                }
            }

            if ($this->priceOverrider) {
                $orderItem = $this->priceOverrider->override($orderItem);
            }

            unset($orderItem->type);
            $this->priceCalculator->addItem($product->price->gross_price_of_packing, $orderItem->quantity);

            $order->items()->save($orderItem);

            if (!empty($product->weight_trade_unit)) {
                $weight += $product->weight_trade_unit * $orderItem->quantity;
            }
        }
        $order->total_price = $this->priceCalculator->getTotal();
        $order->weight = round($weight, 2);
        $order->save();
    }

    public static function getPriceColumns(): array
    {
        return [
            'net_purchase_price_commercial_unit',
            'net_purchase_price_basic_unit',
            'net_purchase_price_calculated_unit',
            'net_purchase_price_aggregate_unit',
            'net_purchase_price_the_largest_unit',
            'net_selling_price_commercial_unit',
            'net_selling_price_basic_unit',
            'net_selling_price_calculated_unit',
            'net_selling_price_aggregate_unit',
            'net_selling_price_the_largest_unit',
            'gross_selling_price_commercial_unit',
            'gross_selling_price_basic_unit',
            'gross_selling_price_calculated_unit',
            'gross_selling_price_aggregate_unit',
            'gross_selling_price_the_largest_unit',
            'net_purchase_price_commercial_unit_after_discounts',
            'net_purchase_price_basic_unit_after_discounts',
            'net_purchase_price_calculated_unit_after_discounts',
            'net_purchase_price_aggregate_unit_after_discounts',
            'net_purchase_price_the_largest_unit_after_discounts'
        ];
    }

    /**
     * @throws Exception
     */
    public static function updateOrderAddress($order, $adressArray, $type, $phone, $relation, $login = '', $forceUpdateEmail = false, $forceUpdateCustomer = false): void
    {
        $phone = $phone ?? $adressArray['phone'];
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (!is_array($adressArray)) {
            $adressArray = [];
        }

        switch ($relation) {
            case 'order':
                $address = $order->addresses()->where('type', $type)->first();
                $obj = $order;
                $exists = (bool)$address;
                if (!$address) {
                    $address = new OrderAddress();
                    $address->phone = $phone;
                    $address->type = $type;
                }
                if (!empty($login) && (!$exists || $forceUpdateEmail)) {
                    $address->email = $login;
                }
                break;
            case 'customer':
                $address = $order->customer->addresses()->where('type', $type)->first();
                $exists = (bool)$address;
                $obj = $order->customer;
                if (!$address) {
                    $address = new CustomerAddress();
                    $address->phone = $phone;
                    $address->type = $type;
                }
                if ($forceUpdateCustomer) {
                    $address->phone = $phone;
                    $address->type = $type;
                    $adressArray['firstname'] = $adressArray['cust_firstname'];
                    $adressArray['lastname'] = $adressArray['cust_lastname'];
                }
                if (!empty($login) && (!$exists || $forceUpdateEmail)) {
                    $address->email = $login;
                }
                break;
            default:
                throw new Exception('Unsupported order address relation');
        }

        foreach ([
             'firstname',
             'lastname',
             'firmname',
             'nip',
             'address',
             'flat_number',
             'city',
             'postal_code'
        ] as $column) {
            if (!empty($adressArray[$column])) {
                $address->$column = $adressArray[$column];
            }
        }

        $obj->addresses()->save($address);
    }
}
