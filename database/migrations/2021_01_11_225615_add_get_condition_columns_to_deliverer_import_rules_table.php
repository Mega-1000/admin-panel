<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGetConditionColumnsToDelivererImportRulesTable extends Migration
{
    public function up(): void
    {
        Schema::table('deliverer_import_rules', function (Blueprint $table) {
            DB::statement(sprintf(
                "ALTER TABLE deliverer_import_rules MODIFY COLUMN `action` ENUM(%s)",
                implode(',', [
                    "'searchCompare'",
                    "'searchRegex'",
                    "'set'",
                    "'get'",
                    "'getAndReplace'",
                    "'getWithCondition'",
                ])
            ));

            $table->unsignedTinyInteger('condition_column_number')->nullable()
                ->after('change_to');
            $table->string('condition_value')->nullable()->after('condition_column_number');
        });
    }

    public function down(): void
    {
        Schema::table('deliverer_import_rules', function (Blueprint $table) {
            DB::statement(sprintf(
                "ALTER TABLE deliverer_import_rules MODIFY COLUMN `action` ENUM(%s)",
                implode([
                    "'searchCompare'",
                    "'searchRegex'",
                    "'set'",
                    "'get'",
                    "'getAndReplace'",
                ])
            ));

            $table->dropColumn([
                'condition_column_number',
                'condition_value',
            ]);
        });
    }
}
