<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuariosPremium extends Model
{
    use HasFactory;
    protected $table = "usuarios_premium";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'id_paquete',
        'pay_payment_id',
        'fecha_creacion',
        'fecha_vencimiento',
        'estado'
    ];
}
