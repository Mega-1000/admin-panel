<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterPacket extends Model
{
    use HasFactory;

    public $fillable = [
        'newsletter_entries_ids',
    ];
}
