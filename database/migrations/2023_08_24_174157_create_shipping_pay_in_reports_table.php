<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('shipping_pay_in_reports', function (Blueprint $table) {
            $table->id();
            $table->string('symbol_spedytora');
            $table->string('numer_listu');
            $table->string('nr_faktury_do_ktorej_dany_lp_zostal_przydzielony');
            $table->date('data_nadania_otrzymania');
            $table->string('nr_i_d');
            $table->decimal('rzeczywisty_koszt_transportu_brutto', 10, 2);
            $table->decimal('wartosc_pobrania', 10, 2);
            $table->string('file');
            $table->binary('reszta');
            $table->string('rodzaj');
            $table->date('invoice_date');
            $table->text('content');
            $table->string('surcharge');
            $table->boolean('found')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_pay_in_reports');
    }
};
