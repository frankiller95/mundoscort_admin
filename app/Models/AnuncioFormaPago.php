<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnuncioFormaPago extends Model
{
    use HasFactory;
    protected $table = "anuncio_forma_pago";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'anuncio_id',
        'forma_pago_id',
        'estado'
    ];
}
