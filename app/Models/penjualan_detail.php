<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class penjualan_detail extends Model
{
    use Uuid;
    protected $guarded 	= [];

    public function barang(){
    	//relasi many to one (Saya adalah anggota dari model ......)
    	return $this->belongsTo('App\Models\barang');
    }

    public function penjualan_retur(){
    	return $this->hasMany('App\Models\penjualan_retur');
    }
}
