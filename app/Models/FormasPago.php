<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormasPago extends Model
{
    use HasFactory;
    protected $table = "formas_pago";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'forma_pago',
        'fecha_creacion',
        'estado'
    ];
}
