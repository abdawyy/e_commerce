<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Traits\Apptraits;

class admin extends Authenticatable
{
    use Apptraits;
    use HasFactory;
    protected $fillable = [
        'username',
        'password',
        'email',
        'created_at',
        'is_active'





    ];
    protected $table = 'admins';

}
