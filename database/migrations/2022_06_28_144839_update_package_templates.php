<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePackageTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('package_templates', function (Blueprint $table) {
            $table->string('allegro_delivery_method', 50)->nullable();
        });
        
        // inpost smart c 55
        \App\Entities\PackageTemplate::where('id', 55)
            ->update(['allegro_delivery_method' => '2488f7b7-5d1c-4d65-b85c-4cbcf253fd93']);
    
        // GLS
        \App\Entities\PackageTemplate::where('id', 56)
            ->update(['allegro_delivery_method' => '059c0d58-6cdb-4955-ab79-9031518f80f3']);
    
        // dpd courier
        \App\Entities\PackageTemplate::where('id', 45)
            ->update(['allegro_delivery_method' => 'c3066682-97a3-42fe-9eb5-3beeccab840c']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('package_templates', function (Blueprint $table) {
            $table->removeColumn('allegro_delivery_method');
        });
    }
}
