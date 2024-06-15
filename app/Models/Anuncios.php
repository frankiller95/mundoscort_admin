<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anuncios extends Model
{
    use HasFactory;

    protected $table = "anuncios";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'imagen_principal',
        'id_localizacion',
        'edad',
        'nombre_apodo',
        'id_nacionalidad',
        'precio',
        'telefono',
        'zona_de_ciudad',
        'disponibilidad',
        'profesion',
        'peso',
        'url_whatsaap',
        'url_telegram',
        'descripcion',
        'fecha_creacion',
        'fecha_reactivacion',
        'id_usuario',
        'estado'
    ];

    /* public function formasPago()
    {
        return $this->belongsToMany(AnuncioFormaPago::class, 'anuncio_forma_pago', 'anuncio_id', 'forma_pago_id');
    }

    public function categorias()
    {
        return $this->belongsToMany(AnuncioCategoria::class, 'anuncio_categoria', 'anuncio_id', 'categoria_id');
    } */
}
