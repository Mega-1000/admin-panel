<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use App\Services\AllegroOrderService;
use App\Services\ProductService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Synchronize allegro returns
 */
class AllegroCustomerReturnsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->customerRepository = app(CustomerRepository::class);
        $this->allegroOrderService = app(AllegroOrderService::class);
        $this->productRepository = app(ProductRepository::class);
        $this->productService = app(ProductService::class);
        $this->orderRepository = app(OrderRepository::class);

        Log::info('Start allegro events synchronization');
        $this->synchronizeAllPaymentId();
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
                    $order = $this->orderRepository->findWhere(['allegro_form_id' => $form['id']])->first();
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
                $order = $this->orderRepository->findWhere(['allegro_form_id' => $return['orderId']])->first();
                if (!empty($order) && (empty($order->refund_id) || empty($order->to_refund))) {
                    $order->refund_id = $return['id'];
                    $order->to_refund = $this->countRefund($return['items']);
                    $order->save();
                    dispatch_now(new AddLabelJob($order, [Label::RETURN_ALLEGRO_ITEMS]));
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
                $order = $this->orderRepository->findWhere(['allegro_form_id' => $cancellation['order']['checkoutForm']['id']])->first();
                if (!empty($order) && !$order->hasLabel(Label::CUSTOMER_CANCELLATION)) {
                    dispatch_now(new AddLabelJob($order, [Label::CUSTOMER_CANCELLATION]));
                    if ($order->hasLabel(Label::ORDER_ITEMS_REDEEMED_LABEL)) {
                        if ($order->hasLabel(50) || $order->hasLabel(49) || $order->hasLabel(47)) {
                            dispatch_now(new AddLabelJob($order, [Label::HOLD_SHIPMENT]));
                        } else {
                            dispatch_now(new RemoveLabelJob($order, [Label::BLUE_HAMMER_ID]));
                            dispatch_now(new AddLabelJob($order, [Label::RED_HAMMER_ID]));
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
                    dispatch_now(new AddLabelJob($order, [Label::RETURN_ALLEGRO_PAYMENTS]));
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
}
