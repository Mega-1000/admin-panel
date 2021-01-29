<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderPackagesRealCostForCompanyTable extends Migration
{
    public function up(): void
    {
        Schema::create('order_packages_real_cost_for_company', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_package_id')->nullable(false);
            $table->unsignedInteger('deliverer_id')->nullable();
            $table->decimal('cost',8,2);
            $table->timestamps();

            $table->foreign('order_package_id')->references('id')
                ->on('order_packages')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('deliverer_id', 'deliverer_id_foreign')
                ->references('id')->on('deliverers')->onUpdate('cascade')->onDelete('cascade');
        });

        $this->moveRealCostForCompanyToNewTable();

        Schema::table('order_packages', function (Blueprint $table) {
            $table->dropColumn('real_cost_for_company');
        });
    }

    public function down(): void
    {
        Schema::table('order_packages', function (Blueprint $table) {
            $table->decimal('real_cost_for_company',8,2)->nullable()
                ->after('cost_for_company')
                ->comment('Realny / naliczony koszt wysylki brutto dla firmy');
        });

        $this->moveRealCostForCompanyToOldTable();

        Schema::table('order_packages_real_cost_for_company', function (Blueprint $table) {
            $table->dropForeign('order_packages_real_cost_for_company_order_package_id_foreign');
            $table->dropForeign('deliverer_id_foreign');
        });

        Schema::dropIfExists('order_packages_real_cost_for_company');
    }

    private function moveRealCostForCompanyToNewTable(): void
    {
        $orderPackages = DB::table('order_packages')->get();
        if (!empty($orderPackages)) {
            foreach($orderPackages as $orderPackage) {
                if ((float) $orderPackage->real_cost_for_company > 0.0) {
                    $currentTimestamp = now();

                    DB::table('order_packages_real_cost_for_company')->insert([
                        'order_package_id' => $orderPackage->id,
                        'cost' => abs($orderPackage->real_cost_for_company),
                        'created_at' => $currentTimestamp,
                        'updated_at' => $currentTimestamp,
                    ]);
                }
            }
        }
    }

    private function moveRealCostForCompanyToOldTable(): void
    {
        $orderPackageCosts = DB::table('order_packages_real_cost_for_company')
            ->groupBy('order_package_id')
            ->orderBy('created_at', 'ASC')->get();

        if (!empty($orderPackageCosts)) {
            foreach ($orderPackageCosts as $item) {
                DB::table('order_packages')->where('id', $item->order_package_id)
                    ->update(['real_cost_for_company' => $item->cost]);
            }
        }
    }
}
