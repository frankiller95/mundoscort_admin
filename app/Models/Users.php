<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    protected $table = "users";

    protected $primaryKey = "id";

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'address',
        'phone',
        'email_verified_at',
        'password',
        'remember_token',
        'rol_id',
        'usuario_premium',
        'created_at',
        'updated_at'
    ];
}
