<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category.
 *
 * @package namespace App\Entities;
 */
class ConfirmPackages extends Model
{
    protected $table = 'confirm_packages';
    protected $fillable = ['package_id'];

    public function package()
    {
        return $this->hasOne(OrderPackage::class, 'id', 'package_id');
    }
}
