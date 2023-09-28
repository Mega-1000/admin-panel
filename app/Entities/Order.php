<?php

namespace App\Entities;

use App\Enums\PackageStatus;
use App\Helpers\TaskTimeHelper;
use App\Traits\SaveQuietlyTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Order.
 *
 * @property int $id
 * @property int $warehouse_id
 * @property int $sello_id
 * @property int $reminder_date
 * @property int $status_id
 * @property \Illuminate\Database\Eloquent\Collection<OrderItem>
 * @property string $shipment_price_for_client
 * @property Chat $chat
 * @property Task $task
 * @property Status $status
 *
 * @property Carbon $created_at
 * @property ?Warehouse $warehouse
 * @property ?Customer $customer
 * @property ?Carbon $preferred_invoice_date
 * @property string $labels_log
 * @property string $consultant_notices
 * @property mixed $factoryDelivery
 * @property Collection $labels
 *
 * @property Collection<OrderPackage> $packages
 *
 * @property Collection<OrderItem> $items
 *
 * @package namespace App\Entities;
 */
class Order extends Model implements Transformable
{
    use TransformableTrait;
    use SaveQuietlyTrait;

    const STATUS_WITHOUT_REALIZATION = 8;
    const COMMENT_SHIPPING_TYPE = 'shipping_comment';
    const COMMENT_WAREHOUSE_TYPE = 'warehouse_comment';
    const COMMENT_CONSULTANT_TYPE = 'consultant_comment';
    const COMMENT_FINANCIAL_TYPE = 'financial_comment';
    const VAT_VALUE = 1.23;

    const PROFORM_DIR = 'public/proforma/';

    public array $customColumnsVisibilities = [
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
        'preferred_invoice_date',
        'token',
        'preliminary_buying_document_number',
        'buying_document_number',
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
        'allegro_payment_id',
        'labels_log',
        'preferred_invoice_date',
        'is_buying_admin_side',
        'packages_values',
    ];

    /**
     * @param Authenticatable|null $user
     * @param String $message
     *
     * @return string
     */
    public static function formatMessage(?Authenticatable $user, string $message): string
    {
        return PHP_EOL . Carbon::now()->toDateTimeString()
            . ($user ? ' ' . $user->name . ' ' . $user->firstname . ' ' . $user->lastname : '')
            . ': ' . $message;
    }

    public function getPackagesCashOnSum()
    {
        $sum = 0;

        foreach ($this->packages as $package) {
            $sum += $package->cash_on_delivery;
        }

        return $this->toPayPackages() - $sum;
    }

    public function orderWarehouseNotifications(): HasMany
    {
        return $this->hasMany(OrderWarehouseNotification::class);
    }

    /**
     * @return float|bool|int
     */
    public function toPayPackages(): float|bool|int
    {
        $sum = 0;
        $packages = $this->packages()->whereIn('status', ['SENDING', 'DELIVERED', 'NEW', 'WAITING_FOR_SENDING'])->get();
        foreach ($packages as $package) {
            $sum += $package->cash_on_delivery;
        }
        $orderTotalPrice = $this->getSumOfGrossValues();
        $totalPaymentAmount = floatval($this->payments()->where('promise', '=', '')->sum("amount"));
        $totalPromisePaymentAmount = floatval($this->payments()->where('declared_sum', '!=', null)->sum("declared_sum"));
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
    public function getSumOfGrossValues(): float
    {
        $totalOfProductsPrices = 0;

        if (count($this->items)) {
            foreach ($this->items as $item) {
                $totalOfProductsPrices += $item->gross_selling_price_commercial_unit * $item->quantity;
            }
        }

        return round($totalOfProductsPrices + floatval($this->shipment_price_for_client) + floatval($this->additional_service_cost) + floatval($this->additional_cash_on_delivery_cost), 2);
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->getSumOfGrossValues();
    }

    /**
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(AllegroDispute::class);
    }

    public function allegroGeneralExpenses(): HasMany
    {
        return $this->hasMany(AllegroGeneralExpense::class);
    }

    /**
     * @return HasMany
     */
    public function paymentsWithTrash()
    {
        return $this->hasMany(OrderPayment::class)->withTrashed();
    }

    /**
     * @return bool
     */
    public function isPaymentRegulated(): bool
    {
        $valueRange = config('orders.plus-minus-regulation-amount');
        $orderTotalPrice = $this->getSumOfGrossValues();
        $totalPaymentAmount = floatval($this->payments()->where("promise", "")->sum("amount"));

        return ($totalPaymentAmount > ($orderTotalPrice - $valueRange) && $totalPaymentAmount < ($orderTotalPrice + $valueRange));
    }

    /**
     * @return float|int
     */
    public function toPay(): float|int
    {
        $orderTotalPrice = $this->getSumOfGrossValues();
        $totalPaymentAmount = floatval($this->payments()->where('promise', '=', '')->sum("amount")) > 2
            ? floatval($this->payments()->where('promise', '=', '')->sum("amount"))
            : floatval($this->payments()->where('declared_sum', '!=', null)->sum("declared_sum"));

        return $orderTotalPrice - $totalPaymentAmount > -2 && $orderTotalPrice - $totalPaymentAmount < 2
            ? 0
            : $orderTotalPrice - $totalPaymentAmount;
    }

    public function isDeliveryDataComplete(): bool
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
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class);
    }

    /**
     * @return Model
     */
    public function getDeliveryAddress(): Model
    {
        return $this->addresses()->where('type', '=', 'DELIVERY_ADDRESS')->first() ?? $this->createEmptyDeliveryAddress();
    }

    /**
     * Create empty delivery address for this order
     *
     * @return Model
     */
    private function createEmptyDeliveryAddress(): Model
    {
        return OrderAddress::create([
            'type' => 'DELIVERY_ADDRESS',
            'order_id' => $this->id,
        ]);
    }

    /**
     * @return Model|HasMany|null
     */
    public function getInvoiceAddress(): Model|HasMany|null
    {
        return $this->addresses()->where('type', '=', 'INVOICE_ADDRESS')->first();
    }

    public function hasLabel($labelId): int
    {
        if (!is_array($labelId)) {
            $labelId = [$labelId];
        }

        return $this->labels()->whereIn('label_id', $labelId)->count();
    }

    /**
     * @return BelongsToMany
     */
    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'order_labels')->withPivot(['added_type', 'created_at']);
    }

    public function orderLabels(): HasMany
    {
        return $this->hasMany(OrderLabel::class);
    }

    public function promisePayments(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->payments()->where('promise', 'like', '1')->get();
    }

    public function hasPromisePayments(): int
    {
        return $this->payments()->where('promise', 'like', '1')->count();
    }

    public function promisePaymentsSum()
    {
        $sum = 0;
        $promisePayments = $this->payments()->where('declared_sum', '!=', null)->get();

        foreach ($promisePayments as $promisePayment) {
            $sum += $promisePayment->declared_sum;
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

    public function getOfferFinanceBilans()
    {
        $bilans = $this->payments()->where('operation_type', '!=', 'Zwrot towaru')->sum('amount');

        return $this->getValue() - $bilans + $this->payments()->where('operation_type', 'Zwrot towaru')->sum('amount');
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

    public function hasOrderSentLP(): bool
    {
        $LPs = $this->packages()->where('status', '!=', 'NEW')->first();

        if (!empty($LPs)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function allegroOrder(): BelongsTo
    {
        return $this->belongsTo(AllegroOrder::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(OrderMessage::class);
    }

    public function monitorNote(): HasOne
    {
        return $this->hasOne(OrderMonitorNote::class);
    }

    public function task(): HasOne
    {
        return $this->hasOne(Task::class);
    }

    public function deliveryAddress(): HasOne
    {
        return $this->hasOne(OrderAddress::class)->where('type', 'DELIVERY_ADDRESS');
    }

    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(OrderInvoice::class, 'order_order_invoices', 'order_id', 'invoice_id');
    }

    public function buyInvoices(): BelongsToMany
    {
        return $this->belongsToMany(OrderInvoice::class, 'order_order_invoices', 'order_id', 'invoice_id')->where('invoice_type', 'buy');
    }

    public function sellInvoices(): BelongsToMany
    {
        return $this->belongsToMany(OrderInvoice::class, 'order_order_invoices', 'order_id', 'invoice_id')->where('invoice_type', 'sell');
    }

    public function files(): HasMany
    {
        return $this->hasMany(OrderFiles::class);
    }

    public function taskSchedule(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function getToken(): string
    {
        if (empty($this->token)) {
            $this->token = Str::random(32);
            static::where('id', $this->id)->update(['token' => $this->token]);
        }

        return $this->token;
    }

    public function factoryDelivery(): HasMany
    {
        return $this->hasMany(OrderOtherPackage::class)->where('type', 'from_factory');
    }

    public function notCalculable()
    {
        return $this->hasMany(OrderOtherPackage::class)->where('type', 'not_calculable');
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
    public function createNewTask($duration, $parentId = null): void
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

    public function commission(): float
    {
        return $this->detailedCommissions()->sum('amount');
    }

    public function detailedCommissions(): HasMany
    {
        return $this->hasMany(OrderAllegroCommission::class);
    }

    public function paymentsTransactions(): HasMany
    {
        return $this->hasMany(OrderPaymentLog::class);
    }

    public function getLastOrder(): int
    {
        return Order::orderBy('id', 'desc')->first()->id;
    }

    public function getPreviousOrderId($orderId): int
    {
        $order = Order::where('id', '<', $orderId)->orderBy('id', 'desc')->first();
        return $order ? $order->id : 0;
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
            $orderProfit += ($item->gross_selling_price_commercial_unit - ($item->net_purchase_price_commercial_unit_after_discounts * self::VAT_VALUE)) * $item->quantity;
        }

        $orderProfit += $this->additional_service_cost;

        return round($orderProfit, 2);
    }

    public function chat(): HasOne
    {
        return $this->hasOne(Chat::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * @return BelongsTo
     */
    public function firmSource(): BelongsTo
    {
        return $this->belongsTo(FirmSource::class);
    }

    public function setDefaultDates($serviceName = 'sello')
    {
        if ($serviceName == 'sello') {
            $dateFrom = Carbon::parse($this->selloTransaction->tr_RemittanceDate);
            $dateTo = (Carbon::parse($this->selloTransaction->tr_RemittanceDate))->addWeekdays(2);
        } else {
            $dateFrom = Carbon::createFromTimestamp($this->created_at->getTimestamp());
            $dateTo = (Carbon::createFromTimestamp($this->created_at->getTimestamp()))->addWeekdays(2);
        }

        $this->dates()->updateOrCreate(['order_id' => $this->id], [
            'customer_shipment_date_from' => $dateFrom,
            'customer_shipment_date_to' => $dateTo,
            'customer_acceptance' => true,
            'consultant_shipment_date_from' => $dateFrom,
            'consultant_shipment_date_to' => $dateTo,
            'consultant_acceptance' => true,
            'message' => 'Ustawiono domyślne daty dla zamówienia'
        ]);
    }

    public function orderDates(): HasOne
    {
        return $this->hasOne(OrderDates::class);
    }

    public function dates(): HasOne
    {
        return $this->hasOne(OrderDates::class);
    }

    public function getProformStoragePathAttribute(): string
    {
        return self::PROFORM_DIR . $this->proforma_filename;
    }

    public function getIsAllegroOrderAttribute(): bool
    {
        return $this->allegro_form_id != null;
    }

    public function orderOffers(): HasMany
    {
        return $this->hasMany(OrderOffer::class)->orderBy('created_at', 'desc');
    }

    public function orderReturn(): HasMany
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function returnPosition(int $position = null): ?OrderReturn
    {
        $return = $this->orderReturn->where('product_stock_position_id', $position);
        return $return->first();
    }

    public function invoiceValues(): HasMany
    {
        return $this->hasMany(OrderInvoiceValue::class);
    }
    public function invoiceDocuments(): HasMany
    {
        return $this->hasMany(OrderInvoiceDocument::class);
    }
}
