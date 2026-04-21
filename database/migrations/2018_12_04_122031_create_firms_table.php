<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
/**
 * Class CreateFirmsTable.
 */
class CreateFirmsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('firms', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->string('symbol')->nullable();
            $table->string('delivery_warehouse')->nullable();
            $table->string('email');
            $table->string('secondary_email')->nullable();
            $table->string('nip')->nullable();
            $table->string('account_number')->nullable();
            $table->enum('status', ['ACTIVE', 'PENDING']);
            $table->string('phone')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->text('notices')->nullable();
            $table->text('secondary_notices')->nullable();
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
		Schema::drop('firms');
	}
}
