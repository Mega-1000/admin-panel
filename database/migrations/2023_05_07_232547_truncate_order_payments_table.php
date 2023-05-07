<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('order_payments_logs')->delete();
        DB::statement('ALTER TABLE order_payments_logs AUTO_INCREMENT = 1');

        DB::statement('TRUNCATE TABLE order_payments');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
