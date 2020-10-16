<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class user_role extends Model
{
    use Uuid;
    protected $guarded 	= [];
    protected $hidden = [
        'id','uuid'
    ];

    public function user(){
    	return $this->belongsTo('App\Models\User');
    }

    public function role(){
    	return $this->belongsTo('App\Models\role');
    }
}
