<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaquetesPremium extends Model
{
    use HasFactory;
    protected $table = "paquetes_premium";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'nombre_paquete',
        'precio',
        'cantidad_dias',
        'estado'
    ];
}
