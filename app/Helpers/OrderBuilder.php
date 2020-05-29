<?php

namespace App\Helpers;

use App\Entities\CustomerAddress;
use App\Entities\Order;
use App\Entities\OrderAddress;
use App\Entities\OrderItem;
use App\Entities\Product;
use App\Helpers\interfaces\iDividable;
use App\Helpers\interfaces\iGetUser;
use App\Helpers\interfaces\iOrderPriceOverrider;
use App\Helpers\interfaces\iOrderTotalPriceCalculator;
use App\Helpers\interfaces\iPostOrderAction;
use App\Helpers\interfaces\iSumable;
use Exception;
use Illuminate\Support\Facades\Log;

class OrderBuilder
{

    /**
     * @var iDividable
     */
    private $packageGenerator;
    /**
     * @var iOrderTotalPriceCalculator
     */
    private $priceCalculator;
    /**
     * @var iOrderPriceOverrider
     */
    private $priceOverrider;
    /**
     * @var iSumable
     */
    private $totalTransportSumCalculator;

    /**
     * @var iGetUser
     */
    private $userSelector;

    /**
     * @var iPostOrderAction
     */
    private $postOrderActions;

    public function setPackageGenerator(iDividable $generator)
    {
        $this->packageGenerator = $generator;
        return $this;
    }

    public function setPriceCalculator(iOrderTotalPriceCalculator $priceCalculator)
    {
        $this->priceCalculator = $priceCalculator;
        return $this;
    }

    public function setPriceOverrider(iOrderPriceOverrider $priceOverrider)
    {
        $this->priceOverrider = $priceOverrider;
        return $this;
    }

    public function setTotalTransportSumCalculator(iSumable $calculator)
    {
        $this->totalTransportSumCalculator = $calculator;
        return $this;
    }

    public function setUserSelector(iGetUser $userSelector)
    {
        $this->userSelector = $userSelector;
        return $this;
    }

    public function setPostOrderActions(iPostOrderAction $postOrderActions)
    {
        $this->postOrderActions = $postOrderActions;
        return $this;
    }

    public function newStore($data)
    {
        if (empty($this->packageGenerator) || empty($this->priceCalculator) || empty($this->userSelector)) {
            throw new Exception('Nie zdefiniowano bazowych komponentów klasy');
        }
        OrderBuilder::setEmptyOrderData($data);
        if (empty($data['order_items']) || !is_array($data['order_items'])) {
            throw new Exception('missing_products');
        }
        $order = null;
        $orderExists = false;
        if (!empty($data['cart_token'])) {
            $order = Order::where('token', $data['cart_token'])->first();
            $order->clearPackages();
            $orderExists = true;
            if (!$order) {
                throw new Exception('wrong_cart_token');
            }
        } else {
            $order = new Order();
        }

        $order->getToken();
        $customer = $this->userSelector->getCustomer($order, $data);
        $order->customer_id = $customer->id;

        if (!$orderExists) {
            $order->status_id = 1;
            OrderBuilder::assignEmployeeToOrder($order, $customer);
        }

        if (!empty($data['shipping_abroad'])) {
            $order->shipping_abroad = 1;
        }

        $order->save();

        if (!empty($data['customer_notices'])) {
            $helper = new MessagesHelper();
            $helper->orderId = $order->id;
            $helper->currentUserId = $customer->id;
            $helper->currentUserType = MessagesHelper::TYPE_CUSTOMER;
            $helper->createNewChat();
            $helper->addMessage($data['customer_notices']);
            $order->labels()->attach(MessagesHelper::MESSAGE_YELLOW_LABEL_ID);
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
                'STANDARD_ADDRESS',
                $data['phone'] ?? '',
                'customer',
                $data['customer_login'] ?? '',
                $data['update_email']
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
        if ($this->postOrderActions) {
            $this->postOrderActions->run($order);
        }
        return ['id' => $order->id, 'canPay' => $canPay ?? false];
    }

    private static function setEmptyOrderData(&$data)
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

    private static function assignEmployeeToOrder($order, $customer)
    {
        $orderCustomerOpenExists = Order::where('customer_id', $customer->id)
            ->whereNotIn('status_id', [6, 8])
            ->whereNotNull('employee_id')
            ->first();

        if (!empty($orderCustomerOpenExists)) {
            $order->employee_id = $orderCustomerOpenExists->employee_id;
        }
    }

    private function assignItemsToOrder($order, $items)
    {
        $weight = 0;
        $orderItems = $order->items();
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
            if (!$product || !$price) {
                throw new Exception('wrong_product_id');
            }

            $orderItem = new OrderItem();
            $orderItem->quantity = $item['amount'];
            $orderItem->product_id = $product->id;
            foreach (OrderBuilder::getPriceColumns() as $column) {
                if (!empty($item['old_price']) && isset($oldPrices[$product->id])) {
                    $orderItem->$column = $oldPrices[$product->id][$column];
                } else {
                    $orderItem->$column = $price->$column;
                }
            }
            if ($this->priceOverrider) {
                $orderItem = $this->priceOverrider->override($orderItem);
            }
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

    public static function updateOrderAddress($order, $deliveryAddress, $type, $phone, $relation, $login = '', $forceUpdateEmail = false)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (!is_array($deliveryAddress)) {
            $deliveryAddress = [];
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
            if (!empty($deliveryAddress[$column])) {
                $address->$column = $deliveryAddress[$column];
            }
        }

        $obj->addresses()->save($address);
    }

    private static function getDefaultProduct()
    {
        $product = Product::getDefaultProduct();
        if (!$product) {
            throw new Exception('wrong_product_id');
        }

        return [['id' => $product->id, 'amount' => 1]];
    }
    public static function getPriceColumns()
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
            'net_purchase_price_commercial_unit_after_discounts',
            'net_purchase_price_basic_unit_after_discounts',
            'net_purchase_price_calculated_unit_after_discounts',
            'net_purchase_price_aggregate_unit_after_discounts',
            'net_purchase_price_the_largest_unit_after_discounts'
        ];
    }

}
