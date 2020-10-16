<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class role_menu extends Model
{
    use Uuid;
    protected $guarded  = [];
    protected $hidden = [
        'id','uuid'
    ];

    public function role(){
    	return $this->belongsTo('App\Models\role');
    }

    public function menu(){
    	return $this->belongsTo('App\Models\menu');
    }
}
