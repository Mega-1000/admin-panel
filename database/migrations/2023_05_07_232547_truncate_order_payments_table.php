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
        DB::statement('DELETE FROM order_payments WHERE id > 0');
        DB::statement('ALTER TABLE order_payments AUTO_INCREMENT = 1');
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
