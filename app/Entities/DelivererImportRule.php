<?php

declare(strict_types=1);

namespace App\Entities;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use App\Domains\DelivererPackageImport\Enums\DelivererRulesColumnNameEnum;
use Illuminate\Database\Eloquent\Model;

class DelivererImportRule extends Model
{
    protected $fillable = [
        'deliverer_id',
        'action',
        'db_column_name',
        'import_column_number',
        'value',
        'change_to',
        'order',
    ];

    public function getAction(): DelivererRulesActionEnum
    {
        return new DelivererRulesActionEnum($this->action);
    }

    public function getColumnName(): DelivererRulesColumnNameEnum
    {
        return new DelivererRulesColumnNameEnum($this->db_column_name);
    }
}
