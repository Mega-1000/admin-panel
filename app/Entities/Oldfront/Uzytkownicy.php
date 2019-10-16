<?php

namespace App\Entities\Oldfront;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Uzytkownicy.
 *
 * @package namespace App\Entities\Oldfront;
 */
class Uzytkownicy extends Model implements Transformable
{
    use TransformableTrait;

    protected $connection = "oldfront";
    protected $table = "uzytkownicy";
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'dostawa_imie',
        'dostawa_nazwisko',
        'dostawa_telefon',
        'dostawa_mail',
        'dostawa_ulica',
        'dostawa_ulica_numer',
        'dostawa_kod_pocztowy',
        'dostawa_miasto',
        'faktura_imie',
        'faktura_nazwisko',
        'faktura_telefon',
        'faktura_mail',
        'faktura_ulica',
        'faktura_ulica_numer',
        'faktura_kod_pocztowy',
        'faktura_miasto',
        'faktura_nazwa_firmy',
        'faktura_nip',
    ];
}
