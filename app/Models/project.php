<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class project extends Model
{
    use Uuid;
    protected $guarded 	= [];

    public function project_item(){
    	return $this->hasMany('App\Models\project_item');
    }

    public function penjualan(){
    	return $this->hasMany('App\Models\penjualan');
    }

    public function lap_penjualan(){
    	return $this->hasMany('App\Models\penjualan')->whereStatus(2)->orderBy('tgl_penjualan','ASC');
    }
}