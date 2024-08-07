<?php

namespace App\Http\Controllers\Api;

use App\Entities\BuyingInvoice;
use App\Entities\Order;
use App\Entities\OrderWarehouseNotification;
use App\Entities\Warehouse;
use App\Entities\WorkingEvents;
use App\Enums\OrderPaymentLogTypeEnum;
use App\Helpers\Exceptions\ChatException;
use App\Helpers\MessagesHelper;
use App\Helpers\OrderLabelHelper;
use App\Helpers\PriceHelper;
use App\Helpers\RecalculateBuyingLabels;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\OrderWarehouseNotification\AcceptShipmentRequest;
use App\Http\Requests\Api\OrderWarehouseNotification\DenyShipmentRequest;
use App\Jobs\DispatchLabelEventByNameJob;
use App\Jobs\OrderStatusChangedToDispatchNotificationJob;
use App\Repositories\OrderInvoiceRepository;
use App\Repositories\OrderRepository;
use App\Repositories\OrderWarehouseNotificationRepository;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\OrderPaymentLogService;
use App\Services\OrderPaymentService;
use App\Services\WorkingEventsService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Psy\Util\Str;


class OrderWarehouseNotificationController extends Controller
{
    use ApiResponsesTrait;

    /**
     * OrderWarehouseNotificationController constructor.
     * @param OrderWarehouseNotificationRepository $orderWarehouseNotificationRepository
     * @param OrderRepository $orderRepository
     * @param OrderInvoiceRepository $orderInvoiceRepository
     */
    public function __construct(
        protected readonly OrderWarehouseNotificationRepository $orderWarehouseNotificationRepository,
        protected readonly OrderRepository                      $orderRepository,
        protected readonly OrderInvoiceRepository               $orderInvoiceRepository
    ) {}

    public function getNotification(int $notificationId): ?OrderWarehouseNotification
    {
        return OrderWarehouseNotification::find($notificationId);
    }

    public function deny(DenyShipmentRequest $request, int $notificationId, MessagesHelper $messagesHelper): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['waiting_for_response'] = false;
            $notification = $this->orderWarehouseNotificationRepository->update($data, $notificationId);

            $messagesHelper->sendAvisationDeny($notification->order->chat, $request->get('customer_notices'));

            /** @var Order $order */
            $order = Order::query()->findOrFail($data['order_id']);
            dispatch(new DispatchLabelEventByNameJob($order, "warehouse-notification-denied"));

            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with deny.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    /**
     * @param $data
     * @throws ChatException
     */
    private function sendMessage($data): void
    {
        $warehouse = Warehouse::findOrFail($data['warehouse_id']);
        $role_id = config('employees_roles')['zamawianie-towaru'];

        $employees = $warehouse->firm->employees->filter(function ($employee) use ($role_id) {
            return $employee->employeeRoles->find($role_id);
        });
        if (empty($employees)) {
            $employees = $warehouse->firm->employees;
        }
        Log::notice('twoja stara 1' . $employees->toArray(), $warehouse->toArray());

        $helper = new MessagesHelper();
        $helper->orderId = $data['order_id'];
        $helper->currentUserId = $employees->first()->id;
        $helper->currentUserType = MessagesHelper::TYPE_EMPLOYEE;
        $helper->createNewChat();
        $helper->addMessage($data['customer_notices']);
        OrderLabelHelper::setYellowLabel($helper->getChat());
    }

    public function accept(AcceptShipmentRequest $request, $notificationId, MessagesHelper $messagesHelper): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['waiting_for_response'] = false;

            $data['realization_date'] = $data['realization_date_from'];
            $data['possible_delay_days'] = Carbon::parse($data['realization_date_from'])->diffInDays(Carbon::parse($data['realization_date_to']));
            $notification = OrderWarehouseNotification::find($notificationId);
            $notification->update($data);

            if (!empty($data['customer_notices'])) {
                $this->sendMessage($data);
            }

            $notification->order->shipment_date = $notification->realization_date;
            $notification->order->shipment_start_days_variation = $notification->possible_delay_days;
            $notification->order->save();
            $notification->order->dates->update([
                'warehouse_shipment_date_from' => Carbon::parse($data['realization_date_from']),
                'warehouse_shipment_date_to' => Carbon::parse($data['realization_date_to']),
                'warehouse_acceptance' => true,
                'message' => 'Magazyn <strong>wprowadził</strong> daty dotyczące przesyłki.'
            ]);

            $order = $notification->order;

            $order->labels()->detach([77]);

            $arr = [];
            AddLabelService::addLabels($order, [53], $arr, []);

            $messagesHelper->sendDateChangeMessageAvization($order->chat, 'magazyn');
            $order->date_accepted = false;
            $order->save();

            /** @var Order $order */
            $order = Order::findOrFail($data['order_id']);
            dispatch(new DispatchLabelEventByNameJob($order, "warehouse-notification-accepted"));

            $messagesHelper->sendAvizationAcceptation($order->chat);

            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with create new order.',
                ['exception' => $e->getMessage(), 'class' => $e->getFile(), 'line' => $e->getLine()]
            );
            die();
        }
    }

    public function sendInvoice(Request $request): JsonResponse
    {
        try {
            $order = Order::findOrFail($request->orderId);

            $file = $request->file('file');
            if ($file !== null) {
                $filename = \Illuminate\Support\Str::random(32) . '.' . $file->getClientOriginalExtension();
                $path = Storage::disk('public')->putFileAs('invoices', $file, $filename);

                if (!$path) {
                    throw new Exception("Failed to store the file");
                }

                Log::info('File stored successfully', ['path' => $path]);

                $invoiceInfo = $this->analyzeInvoiceWithClaudeAI($path, $order);

                $order->invoices()->create([
                    'invoice_type' => 'buy',
                    'invoice_name' => $filename,
                    'is_visible_for_client' => (boolean)$request->isVisibleForClient,
                    'invoice_category' => $invoiceInfo['invoice_type'],
                    'invoice_value' => $invoiceInfo['invoice_value'],
                ]);

                $orders = Order::whereHas('invoices')->where('id', '>', '20000')->get()->count();

                $invoiceRequest = $order->invoiceRequests()->first();

                if (!empty($invoiceRequest) && $invoiceRequest->status === 'MISSING') {
                    $invoiceRequest->update(['status' => 'SENT']);
                }

                dispatch(new DispatchLabelEventByNameJob($order, "new-file-added-to-order"));

                return $this->okResponse();
            }
            Log::error('Problem with send invoice.',
                ['exception' => 'No file in request', 'class' => get_class($this), 'line' => __LINE__]
            );
            throw new Exception('No file in request');
        } catch (Exception $e) {
            Log::error('Problem with send invoice.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            throw $e;
        }
    }

    public function calculateTotalCost(Order $order): float
    {
        $sumOfPurchase = 0;
        $items = $order->items;

        foreach ($items as $item) {
            $pricePurchase = $item->net_purchase_price_commercial_unit_after_discounts ?? 0;
            $quantity = $item->quantity ?? 0;

            $sumOfPurchase += floatval($pricePurchase) * $quantity;
        }

        $totalItemsCost = $sumOfPurchase * 1.23;
        $transportCost = 0;

        if ($order->shipment_price_for_us) {
            $transportCost = floatval($order->shipment_price_for_us);
        }

        return $totalItemsCost + $transportCost;
    }

    private function analyzeInvoiceWithClaudeAI($filePath, $order): array
    {
        try {
            Log::info('Analyzing invoice with Claude AI', ['filePath' => $filePath]);

            $fullPath = Storage::disk('public')->path($filePath);
            Log::info('Full file path', ['fullPath' => $fullPath]);

            if (!Storage::disk('public')->exists($filePath)) {
                throw new Exception("File not found: $fullPath");
            }

            // Extract text from PDF
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();
            $text = $this->cleanText($text);

            // Prepare the request to Claude AI API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => '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',
                'anthropic-version' => '2023-06-01',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-3-5-sonnet-20240620',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => "Analyze the following invoice text and provide a JSON response with the following structure:
                        {
                            \"invoice_type\": \"VAT\" or \"proforma\" or \"Unknown\",
                            \"invoice_name\": \"The invoice number or name, or null if not found\",
                            \"invoice_value\": \"The total invoice value including VAT if it's a VAT invoice, or null for proforma or if not found\"
                        }

                        Only provide the JSON response, no additional text.

                        Invoice text:
                        $text"],
                        ],
                    ],
                ],
                'max_tokens' => 1000,
            ]);

            if ($response->successful()) {
                $result = $response->json();

                if (!isset($result['content'][0]['text'])) {
                    Log::error('Unexpected response structure from Claude AI', ['result' => $result]);
                    throw new Exception('Unexpected response structure from Claude AI');
                }

                $content = $result['content'][0]['text'];

                // Parse the JSON response
                $parsedResponse = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::error('Failed to parse JSON response from Claude AI', [
                        'content' => $content,
                        'error' => json_last_error_msg()
                    ]);
                    throw new Exception('Failed to parse JSON response from Claude AI');
                }
                Log::info([
                    'invoice_type' => $this->sanitizeInvoiceType($parsedResponse['invoice_type'] ?? 'Unknown'),
                    'invoice_name' => $parsedResponse['invoice_name'] ?? null,
                    'invoice_value' => $parsedResponse['invoice_value'] ?? null,
                ]);

                if ($this->sanitizeInvoiceType($parsedResponse['invoice_type'] ?? 'Unknown') == 'Vat') {
                    $buyingInvoice = new BuyingInvoice();
                    $buyingInvoice->order_id = $order->id;
                    $buyingInvoice->invoice_number = $parsedResponse['invoice_name'] ?? null;
                    $buyingInvoice->value = $parsedResponse['invoice_value'] ?? null;
                    $buyingInvoice->analized_by_claute = true;
                    $buyingInvoice->validated_by_nexo = false;
                    $buyingInvoice->file_url = '/storage/invoices' . $filePath;
                    $buyingInvoice->save();

                    RecalculateBuyingLabels::recalculate($order);
                    $order->labels()->detach(290);
                } else {
                    if ($this->calculateTotalCost($order) == $parsedResponse['invoice_value']) {
                        $order->labels()->detach(64);
                    } else {
                        $order->labels()->attach(63);
                    }
                }


                // Validate and sanitize the parsed response
                return [
                    'invoice_type' => $this->sanitizeInvoiceType($parsedResponse['invoice_type'] ?? 'Unknown'),
                    'invoice_name' => $parsedResponse['invoice_name'] ?? null,
                    'invoice_value' => $parsedResponse['invoice_value'] ?? null,
                ];
            } else {
                Log::error('Claude AI API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new Exception('Failed to analyze invoice with Claude AI');
            }
        } catch (Exception $e) {
            Log::error('Error analyzing invoice with Claude AI', [
                'exception' => $e->getMessage(),
                'class' => get_class($this),
                'line' => __LINE__
            ]);
            return [
                'invoice_type' => 'Unknown',
                'invoice_name' => null,
                'invoice_value' => null,
            ];
        }
    }

    private function sanitizeInvoiceType(string $type): string
    {
        $type = strtolower($type);
        return in_array($type, ['vat', 'proforma']) ? ucfirst($type) : 'Unknown';
    }

    private function extractInvoiceType(string $content): string
    {
        if (preg_match('/1\.\s*(VAT|proforma)/i', $content, $matches)) {
            return $matches[1];
        }
        return 'Unknown';
    }

    private function extractInvoiceName(string $content): ?string
    {
        if (preg_match('/2\.\s*(.+)/', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractInvoiceValue(string $content, string $invoiceType): ?string
    {
        if ($invoiceType === 'VAT' && preg_match('/3\.\s*(.+)/', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function cleanText($text): string
    {
        // Remove non-UTF8 characters
        $text = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $text);

        // Convert to UTF-8 if not already
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');
        }

        // Remove any remaining invalid UTF-8 sequences
        $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);

        // Trim whitespace
        $text = trim($text);

        return $text;
    }

    public function changeStatus(Request $request): JsonResponse
    {
        try {
            $orderId = $request->orderId;
            $order = Order::findOrFail($orderId);

            $order->labels()->detach([244, 245, 74, 243, 256, 270]);

            dispatch(new DispatchLabelEventByNameJob($order, "all-shipments-went-out"));

            return $this->okResponse();
        } catch (Exception $e) {
            Log::error('Problem with change order status.',
                ['exception' => $e->getMessage(), 'class' => get_class($this), 'line' => __LINE__]
            );
            die();
        }
    }

    public function createAvisation(Order $order): View
    {
        return view('create-avisation-fast', compact('order'));
    }

    public function storeAvisation(Order $order, Request $request): RedirectResponse
    {
        $shipmentDateTo = Carbon::create($order->dates->warehouse_shipment_date_to ?? $order->dates->customer_shipment_date_to);

        if ($shipmentDateTo->isPast()) {
            $order->dates()->update([
                'customer_shipment_date_from' => Carbon::now(),
                'customer_shipment_date_to' => Carbon::now()->addBusinessDays(7),
            ]);

            $shipmentDateTo = Carbon::now()->addBusinessDays(7);
        }

        WorkingEventsService::createEvent(WorkingEvents::ORDER_PAYMENT_STORE_EVENT, $order->id);
        $type = $request->input('payment-type');
        $promiseDate = Carbon::create($request->get('declared_date'));
        $payer = $order->customer->login;


        if (
            $order->getValue() > ($order->payments()->sum('amount') + $order->payments()->sum('declared_sum'))
        ) {
            if ($order->payments->sum('declared_sum') !== 0 && !empty($request->input('declared_sum', '0'))) {
                $orderPayment = app(OrderPaymentService::class)->payOrder($order->id, $request->input('declared_sum', '0'), $payer,
                    null, true,
                    false, $promiseDate,
                    $type, false,
                );
                $orderPayment->deletable = true;
                $orderPayment->save();

                $orderPaymentAmount = (float)PriceHelper::modifyPriceToValidFormat($request->input('declared_sum'));
                $orderPaymentsSum = $orderPayment->order->payments->sum('declared_sum') - $orderPaymentAmount;

                app(OrderPaymentLogService::class)->create(
                    $order->id,
                    $orderPayment->id,
                    $orderPayment->order->customer_id,
                    $orderPaymentsSum,
                    $orderPaymentAmount,
                    $request->input('created_at') ?: Carbon::now(),
                    $request->input('notices') ?: '',
                    $request->input('declared_sum', '0') ?? '0',
                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                    true,
                );
            }

            if ($order->getValue() > ($order->payments()->sum('amount') + $order->payments()->sum('declared_sum'))) {
                WorkingEventsService::createEvent(WorkingEvents::ORDER_PAYMENT_STORE_EVENT, $order->id);
                $type = $request->input('payment-type');
                $promiseDate = Carbon::create($shipmentDateTo);

                $orderPayment = app(OrderPaymentService::class)->payOrder(
                    $order->id, $order->getValue() - ($order->payments()->sum('amount') + $order->payments()->sum('declared_sum')),
                    $payer,
                    null, true,
                    false, $promiseDate,
                    $type, false,
                );
                $orderPayment->deletable = true;
                $orderPayment->save();

                $orderPaymentAmount = (float)PriceHelper::modifyPriceToValidFormat($request->input('declared_sum'));
                $orderPaymentsSum = $orderPayment->order->payments->sum('declared_sum') - $orderPaymentAmount;

                app(OrderPaymentLogService::class)->create(
                    $order->id,
                    $orderPayment->id,
                    $orderPayment->order->customer_id,
                    $orderPaymentsSum,
                    $orderPaymentAmount,
                    $request->input('created_at') ?: Carbon::now(),
                    $request->input('notices') ?: '',
                    $request->input('declared_sum', '0') ?? '0',
                    OrderPaymentLogTypeEnum::ORDER_PAYMENT,
                    true,
                );
            }
        }


        $order->warehouse_id = Warehouse::where('symbol', $request->input('warehouse-symbol'))->first()->id;
        $order->save();

        OrderWarehouseNotification::create([
            'order_id' => $order->id,
            'warehouse_id' => $order->warehouse_id,
            'employee_id' => $request->get('employee'),
            'waiting_for_response' => true,
        ]);

        $prev = [];
        AddLabelService::addLabels($order, [52], $prev, [], Auth::user()->id);
        AddLabelService::addLabels($order, [73], $prev, [], Auth::user()->id);

        $order->labels()->detach([44, 224, 68, 206]);

        return redirect()->route('orders.index');
    }

    protected function shouldNotifyWithEmail($orderWarehouseNotification, $now): bool
    {
        return $orderWarehouseNotification->created_at->diff($now)->h >= 2; //only schedules that wait longer then 2h
    }

    protected function canNotifyNow($now): bool
    {
        if (!($now->dayOfWeek == 6 || $now->dayOfWeek == 0)) {  //not Saturday nor Sunday
            if ($now->hour >= 7 && $now->hour < 17) {           //only between 9AM and 5PM
                return true;
            }
        }

        return false;
    }
}
