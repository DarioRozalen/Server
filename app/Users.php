<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $fillable = ['name', 'password', 'email', 'rol_id'];

    protected $table = 'users';

    public function passwords()
    {
        return $this->hasMany('App\Passwords');
    }

    public function categories()
    {
        return $this->hasMany('App\Categories');
    }

    public function roles()
    {
        return $this->belongsTo('App\Rols');
    }
}