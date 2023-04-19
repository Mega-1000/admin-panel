<?php

namespace App\Jobs;

use App\Entities\Product;
use App\Entities\Warehouse;
use App\Facades\Mailer;
use App\Mail\SendTableWithProductPriceChangeMail;
use App\Mail\SendToMega1000WarehouseNotFoundMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class CheckPriceChangesInProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    const ROLE_CHANGE_PRICE = 'ZC';
    protected $date;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($date = null)
    {
        $this->date = $date ?? date('Y-m-d');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $products = Product::where('date_of_price_change', '<=', $this->date)->get();

        if (count($products) == 0) {
            return;
        }

        $suppliers = [];

        foreach ($products as $product) {
            if ($product->product_name_supplier !== null && !in_array($product->product_name_supplier,
                    $suppliers)) {
                $suppliers[] = $product->product_name_supplier;
            }
        }

        foreach ($suppliers as $supplier) {
            $warehouse = Warehouse::where('symbol', $supplier)
                ->with(['employees' => function ($q) {
                    $q->with('employeeRoles');
                }])
                ->with('property')
                ->with('firm')
                ->first();
            if ($warehouse) {
                $email = $this->getEmail($warehouse);
                if ($email) {
                    $this->sendEmail($warehouse, $email);
                }
                // TODO Change to configuration
                $this->sendEmail($warehouse, 'info@' . config('app.domain_name'));
            } else {
                Log::notice(
                    'Warehouse not found',
                    ['supplier' => $supplier, 'class' => get_class($this), 'line' => __LINE__]
                );
                Mailer::create()
                    // TODO Change to configuration
                    ->to('info@' . config('app.domain_name'))
                    ->send(new SendToMega1000WarehouseNotFoundMail("Brak danych magazynu " . $supplier, $supplier));
            }
        }
    }

    private function getEmail($warehouse)
    {
        foreach ($warehouse->employees as $employee) {
            foreach ($employee->employeeRoles as $role) {
                if ($role->symbol == self::ROLE_CHANGE_PRICE && !empty($employee->email)) {
                    return $employee->email;
                }
            }
        }
        return $warehouse->property->email ?: $warehouse->firm->email;
    }

    private function sendEmail($warehouse, $email)
    {
        // TODO Change to configuration
        $sendFormWithProducts = rtrim(config('app.front_nuxt_url'), "/") . "/magazyn/aktualizacja-cen/{$warehouse->id}/zaktualizuj";
        Mailer::create()
            ->to($email)
            ->send(
                new SendTableWithProductPriceChangeMail("Prośba o aktualizację cen produktów " . $warehouse->symbol,
                    $sendFormWithProducts,
                    $warehouse->symbol
                )
            );
    }
}
