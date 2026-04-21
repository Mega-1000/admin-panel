<?php

declare(strict_types=1);

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class DelivererImport extends Model
{
    protected $table = 'deliverer_import';

    protected $fillable = [
        'deliverer_id',
        'originalFileName',
        'importFileName',
    ];
}
