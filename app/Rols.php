<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rols extends Model
{
    protected $table = 'rols';
    protected $fillable = ['name'];

    public function users()
    {	
    	// 1:N
        return $this->hasMany('App\Users');
    }
}
