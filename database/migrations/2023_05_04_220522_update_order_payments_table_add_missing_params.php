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
    public function up()
    {
        Schema::table('order_payments', function (Blueprint $table) {
            $table->string('external_payment_id')->nullable()->comment('ID zewnetrznych płatności ( np allegro , bank itp  - nieobligatoryjne');
            $table->string('payer')->nullable()->comment('Platnik - obligaatoryjne');
            $table->dateTime('operation_date')->nullable()->comment('Data dokonania operacji wplaty / wyplaty - obligatoryjne dla wpłat zaksięgowanych');
            $table->string('tracking_number')->nullable()->comment('Numer listu przewozowego firmy spedycyjnej');
            $table->string('operation_id')->nullable()->comment('ID platnosci - nie obligatoryjna format ciag dowolnych zanków');
            $table->float('declared_sum')->nullable()->comment('Kwota deklarowana - nie obligatoryjne fomat liczba do 2 miejsc po przecinku wartosc liczba dodatnia posiada statusy : deklarowana rozliczona deklarowana');
            $table->dateTime('posting_date')->nullable()->comment('Data księgowania - obligatoryjne dla wpłat zaksięgowanych');
            $table->string('operation_type')->nullable();
            $table->text('comments')->nullable();
            $table->unsignedInteger('order_package_id')->nullable()->comment('ID paczki do której przypisana jest wpłata - obcjonalne');
            $table->enum('created_by', ['bank', 'manually', 'allegro', 'shipping'])->default('manually')->comment('Kto utworzył wpłatę - obligatoryjne');
        });
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
