<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderAddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name');
            $table->string('iso2', 2)->nullable();
        });
        
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('phone_code', 15)->after('nip')->nullable();
            $table->mediumInteger('country_id', false, true)
                ->nullable();
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
        
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Poland', 'PL')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Austria', 'AT')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Belgium', 'BE')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Cyprus', 'CY')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Estonia', 'EE')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Finland', 'FI')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('France', 'FR')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Germany', 'DE')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Greece', 'GR')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Ireland', 'IE')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Italy', 'IT')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Latvia', 'LV')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Lithuania', 'LT')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Luxembourg', 'LU')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Malta', 'MT')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('the Netherlands', 'NL')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Portugal', 'PT')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Slovakia', 'SK')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Slovenia', 'SL')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Spain', 'ES')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Denmark', 'DK')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Sweden', 'SE')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Croatia', 'HR')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Norway', 'NO')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Hungary', 'HU')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Czech Republic', 'CZ')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('USA', 'US')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('UK', 'GB')");
        DB::statement("INSERT INTO countries (name, iso2) VALUES ('Ukraine', 'UA')");
    
        // dpd courier
        \App\Entities\PackageTemplate::where('id', 45)
            ->update(['allegro_delivery_method' => '["c3066682-97a3-42fe-9eb5-3beeccab840c","10b73cc6-28d6-11eb-adc1-0242ac120002"]']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn(['phone_code', 'country_id']);
        });
        
        Schema::dropIfExists('countries');
    }
}
