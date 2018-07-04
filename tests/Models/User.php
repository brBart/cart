<?php

namespace Rennokki\Cart\Test\Models;

use Rennokki\Cart\Traits\HasCarts;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasCarts;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}
