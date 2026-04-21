<?php

use Illuminate\Database\Migrations\Migration;
use App\Entities\Tag;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Tag::create(['name' => '[FAQ-LINK]', 'handler' => 'faqLink']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Tag::where('name', '[FAQ-LINK]')->delete();
    }
};
