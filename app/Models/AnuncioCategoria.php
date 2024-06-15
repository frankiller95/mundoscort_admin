<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnuncioCategoria extends Model
{
    use HasFactory;
    protected $table = "anuncio_categoria";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'anuncio_id',
        'categoria_id',
        'estado'
    ];
}
