<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nacionalidades extends Model
{
    use HasFactory;
    protected $table = "nacionalidades";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'nacionalidad',
        'fecha_creacion',
        'estado'
    ];
}
