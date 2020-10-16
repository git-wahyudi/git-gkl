<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class menu_induk extends Model
{
    protected $table = 'menus';
	//relasi one to many (Saya memiliki banyak anggota di model .....)
    public function menu_child(){
    	return $this->hasMany('App\Models\menu', 'id', 'menu_id');
    }
}
