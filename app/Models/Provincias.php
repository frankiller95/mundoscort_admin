<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincias extends Model
{
    use HasFactory;
    protected $table = "provincias";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'provincia',
        'estado'
    ];
}
