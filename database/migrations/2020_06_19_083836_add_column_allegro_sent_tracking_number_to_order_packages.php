<?php

use App\Entities\OrderPackage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAllegroSentTrackingNumberToOrderPackages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_packages', function (Blueprint $table) {
            $table->boolean('tracking_number_sent_to_allegro')->default(0);
        });
        OrderPackage::query()->update(['tracking_number_sent_to_allegro' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_packages', function (Blueprint $table) {
            $table->dropColumn('tracking_number_sent_to_allegro');
        });
    }
}
