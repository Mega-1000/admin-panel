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

    const ROLE_CHANGE_PRICE = 'ZC';

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
            $warehouse = \App\Entities\Warehouse::where('symbol', $supplier)
                ->with(['employees' => function ($q) {
                    $q->with('employeeRoles');
                }])
                ->with('property')
                ->with('firm')
                ->first()
            ;
            if ($warehouse) {
                $email = $this->getEmail($warehouse);
                if ($email) {
                    $this->sendEmail($warehouse, $email);
                }
                $this->sendEmail($warehouse, 'info@' . env('DOMAIN_NAME'));
            } else {
                Log::notice(
                    'Warehouse not found',
                    ['supplier' => $supplier, 'class' => get_class($this), 'line' => __LINE__]
                );
                \Mailer::create()
                    ->to('info@' . env('DOMAIN_NAME'))
                    ->send(new SendToMega1000WarehouseNotFoundMail("Brak danych magazynu " . $supplier, $supplier));
            }
        }
    }

    private function sendEmail($warehouse, $email)
    {
        $sendFormWithProducts = env('FRONT_NUXT_URL') . "/magazyn/aktualizacja-cen/{$warehouse->id}/zaktualizuj";
        \Mailer::create()
            ->to($email)
            ->send(
                new SendTableWithProductPriceChangeMail("Prośba o aktualizację cen produktów " . $warehouse->symbol,
                $sendFormWithProducts,
                $warehouse->symbol
            )
        );
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
}
