<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class role extends Model
{
    use Uuid;
    protected $guarded 	= [];
    protected $hidden = [
        'id'
    ];

    public function role_menu(){
    	return $this->hasMany('App\Models\role_menu');
    }
}
