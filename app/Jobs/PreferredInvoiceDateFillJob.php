<?php

namespace App\Jobs;

use App\Entities\Label;
use App\Repositories\OrderRepository;
use App\Services\Label\AddLabelService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class PreferredInvoiceDateFillJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $this->orderRepository = app(OrderRepository::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $orders = $this->orderRepository->where([["created_at", ">", '2022-06-01']])
            ->doesntHave('labels', 'and', function ($query) {
                $query->whereIn('label_id', [
                    Label::ORDER_ITEMS_REDEEMED_LABEL,
                    Label::ISSUE_ADVANCE_INVOICE
                ]);
            })->get();

        foreach ($orders as $order) {
            try {
                if ($order->allegro_payment_id !== null || $order->transactions->count() > 0) {
                    Log::notice($order->id);
                    $loopPrevention = [];
                    AddLabelService::addLabels($order, [Label::ISSUE_ADVANCE_INVOICE], $loopPrevention, [], Auth::user()?->id);
                    $order->preferred_invoice_date = new Carbon('last day of last month');
                    $order->save();
                }
            } catch (Throwable $th) {
                Log::error('Błąd podczas przypisywania preferowanej daty wystawienia faktury: ' . $th->getMessage(), [
                    'orderId' => $order->id,
                ]);
                continue;
            }
        }
    }
}
