<?php declare(strict_types=1);

namespace App\Entities;

use App\Domains\DelivererPackageImport\Enums\DelivererRulesActionEnum;
use Illuminate\Database\Eloquent\Model;

class DelivererImportRule extends Model
{
    protected $fillable = [
        'deliverer_id',
        'action',
        'db_column_name',
        'import_column_number',
        'value',
        'changeTo',
        'order',
    ];

    public function getAction(): DelivererRulesActionEnum
    {
        return new DelivererRulesActionEnum($this->action);
    }
}
