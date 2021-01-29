<?php

declare(strict_types=1);

namespace App\Entities;

use App\Enums\PackageStatus;
use App\Helpers\TaskTimeHelper;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Order.
 *
 * @package namespace App\Entities;
 */
class Order extends Model implements Transformable
{

    use TransformableTrait;

    const STATUS_WITHOUT_REALIZATION = 8;
    const STATUS_ORDER_FINISHED = 6;
    const COMMENT_SHIPPING_TYPE = 'shipping_comment';
    const COMMENT_WAREHOUSE_TYPE = 'warehouse_comment';
    const COMMENT_CONSULTANT_TYPE = 'consultant_comment';
    const COMMENT_FINANCIAL_TYPE = 'financial_comment';
    public $customColumnsVisibilities = [
        'mark',
        'spedition_exchange_invoiced_selector',
        'packages_sent',
        'packages_not_sent',
        'print',
        'name',
        'orderDate',
        'orderId',
        'actions',
        'section',
        'statusName',
        'symbol',
        'customer_notices',
        'consultant_notices',
        'financial_comment',
        'clientPhone',
        'clientEmail',
        'clientFirstname',
        'clientLastname',
        'nick_allegro',
        'profit',
        'weight',
        'products_value_gross',
        'additional_service_cost',
        'additional_cash_on_delivery_cost',
        'shipment_price_for_client',
        'shipment_price_for_us',
        'sum_of_gross_values',
        'sum_of_payments',
        'left_to_pay',
        'transport_exchange_offers',
        'shipment_date',
        'label_platnosci',
        'label_produkcja',
        'label_transport',
        'label_info_dodatkowe',
        'label_fakury_zakupu',
        'invoices',
        'invoice_gross_sum',
        'icons',
        'consultant_earning',
        'difference',
        'correction_amount',
        'correction_description',
        'document_number',
        'search_on_lp',
        'production_date',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_from_front_db',
        'customer_id',
        'status_id',
        'last_status_update_date',
        'total_price',
        'weight',
        'shipment_price_for_client',
        'shipment_price_for_us',
        'customer_notices',
        'cash_on_delivery_amount',
        'allegro_transaction_id',
        'employee_id',
        'warehouse_id',
        'additional_service_cost',
        'invoice_warehouse_file',
        'document_number',
        'consultant_earning',
        'consultant_earning',
        'printed',
        'correction_description',
        'correction_amount',
        'packing_warehouse_cost',
        'rating',
        'rating_message',
        'shipping_abroad',
        'proposed_payment',
        'additional_cash_on_delivery_cost',
        'consultant_notices',
        'remainder_date',
        'shipment_date',
        'shipment_start_days_variation',
        'invoice_id',
        'additional_info',
        'invoice_number',
        'print_order',
        'consultant_notice',
        'consultant_value',
        'refund_id',
        'refunded',
        'warehouse_notice',
        'warehouse_value',
        'production_date',
        'master_order_id',
        'spedition_comment',
        'financial_comment',
        'allegro_form_id',
        'allegro_deposit_value',
        'allegro_operation_date',
        'allegro_additional_service',
    ];

    /**
     * @param Authenticatable|null $user
     * @param String $message
     * @return string
     */
    public static function formatMessage(?Authenticatable $user, string $message): string
    {
        return PHP_EOL . Carbon::now()->toDateTimeString() . ' ' . $user->name . ' ' . $user->firstname . ' ' . $user->lastname . ': ' . $message;
    }

    public function getPackagesCashOnSum()
    {
        $sum = 0;

        foreach ($this->packages as $package) {
            $sum += $package->cash_on_delivery;
        }

        return $this->toPayPackages() - $sum;
    }

    /**
     * @return bool
     */
    public function toPayPackages()
    {
        $sum = 0;
        $packages = $this->packages()->whereIn('status', ['SENDING', 'DELIVERED', 'NEW', 'WAITING_FOR_SENDING'])->get();
        foreach ($packages as $package) {
            $sum += $package->cash_on_delivery;
        }
        $orderTotalPrice = $this->getSumOfGrossValues();
        $totalPaymentAmount = floatval($this->payments()->where('promise', '=', '')->sum("amount"));
        $totalPromisePaymentAmount = floatval($this->payments()->where('promise', '=', '1')->sum("amount"));
        //dd($orderTotalPrice - $totalPromisePaymentAmount - $sum);
        if ($orderTotalPrice - $totalPaymentAmount > -2 && $orderTotalPrice - $totalPaymentAmount < 2) {
            return 0;
        } else if ($totalPaymentAmount < 2 && $totalPromisePaymentAmount > 2) {
            return $orderTotalPrice - $totalPromisePaymentAmount - $sum;
        } else {
            return $orderTotalPrice - $totalPaymentAmount - $sum;
        }
    }

    public function packages(): HasMany
    {
        return $this->hasMany(OrderPackage::class);
    }

    /**
     * @return float
     */
    public function getSumOfGrossValues()
    {
        $totalOfProductsPrices = 0;

        if (count($this->items)) {
            foreach ($this->items as $item) {
                $totalOfProductsPrices += $item->gross_selling_price_commercial_unit * intval($item->quantity);
            }
        }

        return round($totalOfProductsPrices + floatval($this->shipment_price_for_client) + floatval($this->additional_service_cost) + floatval($this->additional_cash_on_delivery_cost), 2);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    /**
     * @return bool
     */
    public function isPaymentRegulated()
    {
        $valueRange = config('orders.plus-minus-regulation-amount');
        $orderTotalPrice = $this->getSumOfGrossValues();
        $totalPaymentAmount = floatval($this->payments()->where("promise", "")->sum("amount"));

        return ($totalPaymentAmount > ($orderTotalPrice - $valueRange) && $totalPaymentAmount < ($orderTotalPrice + $valueRange));
    }

    /**
     * @return bool
     */
    public function toPay()
    {
        $orderTotalPrice = $this->getSumOfGrossValues();
        if (floatval($this->payments()->where('promise', '=', '')->sum("amount")) > 2) {
            $totalPaymentAmount = floatval($this->payments()->where('promise', '=', '')->sum("amount"));
        } else {
            $totalPaymentAmount = floatval($this->payments()->where('promise', '=', '1')->sum("amount"));
        }
        if ($orderTotalPrice - $totalPaymentAmount > -2 && $orderTotalPrice - $totalPaymentAmount < 2) {
            return 0;
        } else {
            return $orderTotalPrice - $totalPaymentAmount;
        }
    }

    public function isDeliveryDataComplete()
    {
        $deliveryAddress = $this->addresses()->where('type', '=', 'DELIVERY_ADDRESS')->first();
        return (!(
            empty($this->shipment_date) ||
            empty($deliveryAddress->firstname) ||
            empty($deliveryAddress->lastname) ||
            empty($deliveryAddress->phone) ||
            empty($deliveryAddress->address) ||
            empty($deliveryAddress->flat_number) ||
            empty($deliveryAddress->city) ||
            empty($deliveryAddress->postal_code)
        ));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }

    public function isInvoiceDataComplete()
    {
        $invoiceAddress = $this->addresses()->where('type', '=', 'INVOICE_ADDRESS')->first();
        return (!(
            empty($invoiceAddress->firstname) ||
            empty($invoiceAddress->lastname) ||
            empty($invoiceAddress->phone) ||
            empty($invoiceAddress->address) ||
            empty($invoiceAddress->flat_number) ||
            empty($invoiceAddress->city) ||
            empty($invoiceAddress->postal_code)
        ));
    }

    public function getDeliveryAddress()
    {
        return $this->addresses()->where('type', '=', 'DELIVERY_ADDRESS')->first();
    }

    public function getInvoiceAddress()
    {
        return $this->addresses()->where('type', '=', 'INVOICE_ADDRESS')->first();
    }

    public function hasLabel($labelId)
    {
        return $this->labels()->where('label_id', $labelId)->count();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function labels()
    {
        return $this->belongsToMany(Label::class, 'order_labels')->withPivot('added_type');
    }

    public function promisePayments()
    {
        $promisePayments = $this->payments()->where('promise', 'like', '1')->get();

        return $promisePayments;
    }

    public function hasPromisePayments()
    {
        return $this->payments()->where('promise', 'like', '1')->count();
    }

    public function promisePaymentsSum()
    {
        $sum = 0;
        $promisePayments = $this->payments()->where('promise', 'like', '1')->get();

        foreach ($promisePayments as $promisePayment) {
            $sum += $promisePayment->amount;
        }

        return $sum;
    }

    public function bookedPayments()
    {
        $bookedPayments = $this->payments()->where('promise', 'like', '')->get();

        return $bookedPayments;
    }

    public function hasBookedPayments()
    {
        return $this->payments()->where('promise', 'like', '')->count();
    }

    public function packagesCashOnDeliverySum()
    {
        $sum = 0;
        $packages = $this->packages()->whereIn('status', ['SENDING', 'DELIVERED', 'NEW', 'WAITING_FOR_SENDING'])->get();
        foreach ($packages as $package) {
            $sum += $package->cash_on_delivery;
        }

        return $sum;
    }

    public function bookedPaymentsSum()
    {
        $sum = 0;
        $promisePayments = $this->payments()->where('promise', 'like', '')->get();

        foreach ($promisePayments as $promisePayment) {
            $sum += $promisePayment->amount;
        }

        return $sum;
    }

    public function orderGroupBookedPaymentsSum()
    {
        $sum = 0;
        $promisePayments = $this->payments()->where('promise', 'like', '')->get();

        foreach ($promisePayments as $promisePayment) {
            $sum += $promisePayment->amount;
        }

        return $sum;
    }

    public function hasOrderSentLP()
    {
        $LPs = $this->packages()->where('status', '!=', 'NEW')->first();

        if (!empty($LPs)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(OrderMessage::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function monitorNote()
    {
        return $this->hasOne(OrderMonitorNote::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function task()
    {
        return $this->hasOne(OrderTask::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function deliveryAddress()
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'DELIVERY_ADDRESS');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invoices()
    {
        return $this->belongsToMany(OrderInvoice::class, 'order_order_invoices', 'order_id', 'invoice_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function buyInvoices()
    {
        return $this->belongsToMany(OrderInvoice::class, 'order_order_invoices', 'order_id', 'invoice_id')->where('invoice_type', 'buy');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sellInvoices()
    {
        return $this->belongsToMany(OrderInvoice::class, 'order_order_invoices', 'order_id', 'invoice_id')->where('invoice_type', 'sell');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->hasMany(OrderFiles::class);
    }

    public function taskSchedule()
    {
        return $this->hasMany(Task::class);
    }

    public function getToken()
    {
        if (empty($this->token)) {
            $this->token = Str::random(32);
            static::where('id', $this->id)->update(['token' => $this->token]);
        }
        return $this->token;
    }

    public function factoryDelivery()
    {
        return $this->hasMany('App\Entities\OrderOtherPackage')->where('type', 'from_factory');
    }

    public function notCalculable()
    {
        return $this->hasMany('App\Entities\OrderOtherPackage')->where('type', 'not_calculable');
    }

    public function getTransportPrice()
    {
        $factoryPrice = $this->factoryDelivery->reduce(function ($curr, $next) {
            return $curr + $next->price;
        }, 0);
        $packagesPrice = $this->packages->reduce(function ($curr, $next) {
            return $curr + $next->cost_for_client;
        }, 0);
        return $factoryPrice + $packagesPrice;
    }

    public function otherPackages()
    {
        return $this->hasMany('App\Entities\OrderOtherPackage');
    }

    public function clearPackages()
    {
        if ($this->sello_id) {
            return;
        }

        $this->otherPackages->map(function ($package) {
            $package->products()->detach();
            $package->delete();
        });
        $allowedStatuses = ['NEW', 'WAITING_FOR_CANCELLED', 'CANCELLED'];
        $fail = $this->packages->first(function ($item) use ($allowedStatuses) {
            return !in_array($item->status, $allowedStatuses);
        });
        if ($fail) {
            return;
        }
        $this->packages->map(function ($package) {
            if ($package->status == 'NEW') {
                $package->packedProducts()->detach();
                $package->delete();
            }
        });
    }

    public function warehousePayments()
    {
        return $this->hasMany(OrderPayment::class)->where('type', 'WAREHOUSE');
    }

    public function speditionPayments()
    {
        return $this->hasMany(OrderPayment::class)->where('type', 'SPEDITION');
    }

    public function speditionPaymentsSum()
    {
        return $this->hasMany(OrderPayment::class)->where('type', 'SPEDITION');
    }

    public function isOrderHasLabel($labelId)
    {
        return $this->labels()->where('labels.id', $labelId)->count() > 0;
    }

    public function invoiceRequests()
    {
        return $this->hasOne(InvoiceRequest::class);
    }

    public function subiektInvoices()
    {
        return $this->hasMany(SubiektInvoices::class);
    }

    public function selloTransaction()
    {
        return $this->hasOne(SelTransaction::class, 'id', 'sello_id');
    }

    public function groupWarehousePayments()
    {
        $acceptedPaymentsValue = 0;
        $pendingPaymentsValue = 0;
        foreach ($this->warehousePayments as $payment) {
            switch ($payment->status) {
                case 'ACCEPTED':
                    $acceptedPaymentsValue += $payment->amount;
                    break;
                case 'PENDING':
                    $pendingPaymentsValue += $payment->amount;
                    break;
            }
        }

        return [
            'ACCEPTED' => $acceptedPaymentsValue,
            'PENDING' => $pendingPaymentsValue
        ];
    }

    /**
     */
    public function createNewTask($duration, $parentId): void
    {
        $date = Carbon::now();
        $task = Task::create([
            'warehouse_id' => Warehouse::OLAWA_WAREHOUSE_ID,
            'user_id' => User::OLAWA_USER_ID,
            'order_id' => $this->id,
            'created_by' => 1,
            'name' => $this->id . ' - ' . $date->format('d-m'),
            'color' => Task::DEFAULT_COLOR,
            'status' => Task::WAITING_FOR_ACCEPT,
            'parent_id' => $parentId
        ]);
        $time = TaskTimeHelper::getFirstAvailableTime($duration);
        TaskTime::create([
            'task_id' => $task->id,
            'date_start' => $time['start'],
            'date_end' => $time['end']
        ]);
        TaskSalaryDetails::create([
            'task_id' => $task->id,
            'consultant_value' => 0,
            'warehouse_value' => 0
        ]);
    }

    public function detailedCommissions()
    {
        return $this->hasMany(OrderAllegroCommission::class);
    }

    public function commission(): float
    {
        return $this->detailedCommissions()->sum('amount');
    }

    public function getLastOrder(): int
    {
        return Order::orderBy('id', 'desc')->first()->id;
    }

    public function getPreviousOrderId($orderId): int
    {
        return Order::where('id', '<', $orderId)->orderBy('id', 'desc')->first()->id;
    }

    public function getSentPackages(): Collection
    {
        return $this->hasMany(OrderPackage::class)->whereIn('status', [PackageStatus::DELIVERED, PackageStatus::SENDING])->get();
    }

    public function getItemsGrossValue(): float
    {
        $totalOfProductsPrices = 0;

        foreach ($this->items as $item) {
            $totalOfProductsPrices += $item->gross_selling_price_commercial_unit * intval($item->quantity);
        }

        return round($totalOfProductsPrices, 2);
    }

    public function getOrderProfit(): float
    {
        $orderProfit = 0;

        foreach ($this->items as $item) {
            $orderProfit += ($item->gross_selling_price_commercial_unit - ($item->net_purchase_price_commercial_unit * 1.23)) * $item->quantity;
        }

        $orderProfit += $this->additional_service_cost;

        return round($orderProfit, 2);
    }
}
