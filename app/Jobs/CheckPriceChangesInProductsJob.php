<?php

namespace App\Jobs;

use App\Mail\SendTableWithProductPriceChangeMail;
use App\Mail\SendToMega1000WarehouseNotFoundMail;
use App\Repositories\WarehouseRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CheckPriceChangesInProductsJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
    public function handle(WarehouseRepository $warehouseRepository)
    {
        $products = \App\Entities\Product::where('date_of_price_change', '<=', $this->date)->get();

        if (count($products) == 0) {
            return;
        }

        $suppliers = [];

        foreach ($products as $product) {
            if ($product->product_name_supplier !== null && !in_array($product->product_name_supplier,
                    $suppliers)) {
                array_push($suppliers, $product->product_name_supplier);
            }
        }

        foreach ($suppliers as $supplier) {
            $warehouse = $warehouseRepository->findByField('symbol', $supplier);
            if (!empty($warehouse->first->id->id)) {
                $sendFormWithProducts = env('FRONT_NUXT_URL') . "/magazyn/aktualizacja-cen/{$warehouse->first->id->id}/zaktualizuj";
                if ($warehouse->first->id->property->email !== null) {
                    if ($this->date == null) {
                        \Mailer::create()
                            ->to($warehouse->first->id->property->email)
                            ->send(new SendTableWithProductPriceChangeMail("Prośba o aktualizację cen produktów " . $warehouse->first->id->symbol,
                                $sendFormWithProducts, $warehouse->first->id->symbol));
                    }
                    \Mailer::create()
                        ->to('info@' . env('DOMAIN_NAME'))
                        ->send(new SendTableWithProductPriceChangeMail("Prośba o aktualizację cen produktów " . $warehouse->first->id->symbol,
                            $sendFormWithProducts, $warehouse->first->id->symbol));
                }
            } else {
                Log::notice(
                    'Warehouse not found',
                    ['supplier' => $supplier, 'class' => get_class($this), 'line' => __LINE__]
                );
                \Mailer::create()
                    ->to('info@' . env('DOMAIN_NAME'))
                    ->send(new SendToMega1000WarehouseNotFoundMail("Brak danych magazynu " . $supplier,
                        $supplier));
            }
        }
    }
}
