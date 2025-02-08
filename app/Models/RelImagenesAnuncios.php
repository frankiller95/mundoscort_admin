<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelImagenesAnuncios extends Model
{
    use HasFactory;
    protected $table = "rel_imagenes_anuncios";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'id_anuncio',
        'path_imagen',
        'nombre_imagen',
        'estado',
        'fecha_registro'
    ];
}
