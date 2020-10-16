<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class menu extends Model
{
    use Uuid;
    protected $guarded 	= [];
    protected $hidden = [
        'id','uuid'
    ];

    public function role_menu(){
    	return $this->hasMany('App\Models\role_menu');
    }

    public function menu_parent(){
    	//relasi many to one (Saya adalah anggota dari model ......)
    	return $this->belongsTo('App\Models\menu_induk', 'menu_id', 'id');
    	
    }
}