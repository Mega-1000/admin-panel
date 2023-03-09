<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\EmailSettingsEnum;

class EmailSetting extends Model
{
    use HasFactory, SoftDeletes;
   
    protected $attribute = [
        'statusTitle'
    ];

    protected $fillable = [
        'status', 
        'time',
        'title',
        'content'
    ];

    public function getStatusTitleAttribute(): string
    {
        $status = '';
        if($this->status=='NEW'){
            $status = EmailSettingsEnum::STATUS_NEW;
        }
        if($this->status=='PRODUCED'){
            $status = EmailSettingsEnum::STATUS_PRODUCED;
        }
        if($this->status=='PICKED_UP')
        {
            $status = EmailSettingsEnum::STATUS_PICKED_UP;
        }
        if($this->status=='PROVIDED'){
            $status = EmailSettingsEnum::STATUS_PROVIDED;
        }

        return $status;
    }
}
