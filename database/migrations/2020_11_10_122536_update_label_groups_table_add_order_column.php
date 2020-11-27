<?php declare(strict_types=1);

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateLabelGroupsTableAddOrderColumn extends Migration
{
    public function up(): void
    {
        Schema::table('label_groups', function (Blueprint $table) {
            $table->unsignedInteger('order')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('label_groups', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
}
