<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Passwords extends Model
{
    protected $table = 'passwords';
    protected $fillable = ['title, password'];

    public function categories()
    {   
        // N:1
        return $this->belongsTo('App\Categories');
    }

    public function users()
    {
        return $this->belongsTo('App\Users');
    }
}