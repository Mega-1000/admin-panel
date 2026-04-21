<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tableName = 'archive_order_payments';
    private $oldTableName = 'order_payments';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the new table
        DB::statement('CREATE TABLE ' . $this->tableName . ' LIKE ' . $this->oldTableName);

        // Copy the data from the old table to the new table
        DB::statement('INSERT INTO ' . $this->tableName . ' SELECT * FROM ' . $this->oldTableName);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP TABLE IF EXISTS ' . $this->tableName );
    }
};
