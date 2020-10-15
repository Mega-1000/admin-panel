<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAllegroPackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allegro_package', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('allegro_operation_date')->comment('Data operacji Allegro pobierana z pliku CSV');
            $table->string('package_spedition_company_name');
            $table->string('package_delivery_company_name');
            $table->decimal('real_total_delivery_company_cost', 9, 2);
            $table->decimal('real_delivery_company_cost', 9, 2);
            $table->decimal('allegro_subscription_cost', 9, 2);
            $table->decimal('ads_campaign_fee', 9, 2)->comment('Opłata za kampanie ADS');
            $table->decimal('bill_correction', 9, 2)->comment('Korekta rachunku');
            $table->decimal('preference_auction_fee', 9, 2)->comment('Opłata za wyróżnienie aukcji Allegro');
            $table->decimal('booked_payment', 9, 2)->comment('Wpłata zaksiegowana (za faktury ALLEGRO )');
            $table->decimal('month_summary', 9, 2)->comment('Podsumowanie miesiąca ALLEGRO');
            $table->text('allegro_transaction_id');
            $table->text('allegro_offer_name');
            $table->decimal('return_of_commission_cost');
            $table->unsignedInteger('package_id');
            $table->foreign('package_id')->references('id')->on('order_packages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allegro_package');
    }
}
