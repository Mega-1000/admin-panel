<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingPayInReport extends Model
{
    use HasFactory;

    public $fillable = [
        'symbol_spedytora',
        'numer_listu',
        'nr_faktury_do_ktorej_dany_lp_zostal_przydzielony',
        'data_nadania_otrzymania',
        'nr_i_d',
        'rzeczywisty_koszt_transportu_brutto',
        'wartosc_pobrania',
        'file',
        'reszta',
        'rodzaj',
        'invoice_date',
        'content',
        'surcharge',
        'found',
    ];

    public static function getColumns(): array
    {
        return [
            'id',
            'symbol_spedytora',
            'numer_listu',
            'nr_faktury_do_ktorej_dany_lp_zostal_przydzielony',
            'data_nadania_otrzymania',
            'nr_i_d',
            'rzeczywisty_koszt_transportu_brutto',
            'wartosc_pobrania',
            'file',
            'reszta',
            'rodzaj',
            'invoice_date',
            'content',
            'surcharge',
            'found',
        ];
    }
}
