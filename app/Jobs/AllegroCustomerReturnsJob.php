<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\AllegroOrderService;
use App\Services\Label\AddLabelService;
use App\Services\Label\RemoveLabelService;
use App\Services\ProductService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Synchronize allegro returns
 */
class AllegroCustomerReturnsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected ?int $userId;
    /**
     * @var CustomerRepository
     */
    private $customerRepository;
    /**
     * @var AllegroOrderService
     */
    private $allegroOrderService;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var ProductService
     */
    private $productService;
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userId = Auth::user()?->id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (Auth::user() === null && $this->userId !== null) {
            Auth::loginUsingId($this->userId);
        }

        $this->customerRepository = app(CustomerRepository::class);
        $this->allegroOrderService = app(AllegroOrderService::class);
        $this->productRepository = app(ProductRepository::class);
        $this->productService = app(ProductService::class);
        $this->orderRepository = app(OrderRepository::class);

        Log::info('Start allegro events synchronization');
        $current_time = Carbon::now();
        if ($current_time->timestamp > strtotime('00:00am') && $current_time->timestamp < strtotime('01:00am')) {
            $this->synchronizeAllPaymentId();
        }
        $this->customerReturns();
        $this->paymentsReturns();
        $this->buyerCancellation();
        Log::info('End allegro events synchronization');
    }

    /**
     * Synchronize all payments
     *
     * @return void
     */
    private function synchronizeAllPaymentId()
    {
        $limit = 100;
        $offset = 0;

        do {
            $response = $this->allegroOrderService->getAllOrders($limit, $offset);

            foreach ($response['checkoutForms'] as $form) {
                try {
                    $order = $this->orderRepository->findWhere([['allegro_form_id', 'like', '%' . $form['id'] . '%']])->first();
                    if (!empty($order) && (empty($order->allegro_payment_id) || empty($order->allegro_operation_date))) {
                        $order->allegro_operation_date = $form['lineItems'][0]['boughtAt'];
                        $order->allegro_additional_service = $form['delivery']['method']['name'];
                        $order->payment_channel = $form['payment']['provider'];
                        $order->allegro_payment_id = $form['payment']['id'];
                        $order->save();
                    }
                } catch (Throwable $ex) {
                    Log::error($ex->getMessage(), [
                        'line' => $ex->getLine(),
                        'file' => $ex->getFile()
                    ]);
                    continue;
                }
            }
            $offset += $limit;
            $totalCount = $response['totalCount'];
        } while ($offset < $totalCount);
    }

    /**
     * Synchronize customer returns
     *
     * @return void
     */
    private function customerReturns()
    {
        $returns = $this->allegroOrderService->getCustomerReturns();
        foreach ($returns as $return) {
            try {
                $order = $this->orderRepository->findWhere([['allegro_form_id', 'like', '%' . $return['orderId'] . '%']])->first();
                if (!empty($order) && ((empty($order->refund_id) || empty($order->to_refund)))) {
                    $order->refund_id = $return['referenceNumber'];
                    $order->to_refund = $this->countRefund($return['items']);
                    $order->save();
                    $prev = [];
                    AddLabelService::addLabels($order, [Label::RETURN_ALLEGRO_ITEMS], $prev, [], Auth::user()->id);
                }
            } catch (Throwable $ex) {
                Log::error($ex->getMessage(), [
                    'line' => $ex->getLine(),
                    'file' => $ex->getFile()
                ]);
                continue;
            }
        }
    }

    /**
     * @param array $items
     *
     * @return float
     */
    private function countRefund(array $items): float
    {
        $sum = 0;
        foreach ($items as $item) {
            $sum += $item['quantity'] * $item['price']['amount'];
        }

        return $sum;
    }

    /**
     * Synchronize payment returns
     *
     * @return void
     */
    private function paymentsReturns(): void
    {
        $returns = $this->allegroOrderService->getPaymentsRefunds();

        foreach ($returns as $return) {
            try {
                $order = $this->orderRepository->findWhere(['allegro_payment_id' => $return['payment']['id']])->first();

                if (!empty($order) && (empty($order->return_payment_id) || empty($order->refunded))) {
                    $order->return_payment_id = $return['id'];
                    $order->refunded = $return['totalValue']['amount'];
                    $order->save();

                    $prev = [];
                    AddLabelService::addLabels($order, [Label::RETURN_ALLEGRO_PAYMENTS], $prev, [], Auth::user()->id);
                }
            } catch (Throwable $ex) {
                Log::error($ex->getMessage(), [
                    'line' => $ex->getLine(),
                    'file' => $ex->getFile()
                ]);
                continue;
            }
        }
    }

    /**
     * Synchronize customer returns
     *
     * @return void
     */
    private function buyerCancellation()
    {
        $cancellations = $this->allegroOrderService->getBuyerCancelled();

        foreach ($cancellations as $cancellation) {
            try {
                $order = $this->orderRepository->findWhere([['allegro_form_id', 'like', '%' . $cancellation['order']['checkoutForm']['id'] . '%']])->first();
                if (!empty($order) && !$order->hasLabel(Label::CUSTOMER_CANCELLATION)) {

                    $prev = [];
                    AddLabelService::addLabels($order, [Label::CUSTOMER_CANCELLATION], $prev, [], Auth::user()->id);
                    if ($order->hasLabel(Label::ORDER_ITEMS_REDEEMED_LABEL)) {
                        if ($order->hasLabel(50) || $order->hasLabel(49) || $order->hasLabel(47)) {
                            AddLabelService::addLabels($order, [Label::HOLD_SHIPMENT], $prev, [], Auth::user()->id);
                        } else {
                            RemoveLabelService::removeLabels($order, [Label::BLUE_HAMMER_ID], $prev, [], Auth::user()->id);
                            AddLabelService::addLabels($order, [Label::RED_HAMMER_ID], $prev, [], Auth::user()->id);
                            $order->task->delete();
                        }

                    }
                }
            } catch (Throwable $ex) {
                Log::error($ex->getMessage(), [
                    'line' => $ex->getLine(),
                    'file' => $ex->getFile()
                ]);
                continue;
            }
        }
    }
}
