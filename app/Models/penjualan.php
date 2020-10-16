<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class penjualan extends Model
{
    use Uuid;
    protected $guarded 	= [];

    public function project(){
    	return $this->belongsTo('App\Models\project');
    }

    public function customer(){
    	return $this->belongsTo('App\Models\customer');
    }

    public function penjualan_detail(){
    	return $this->hasMany('App\Models\penjualan_detail');
    }

    public function list_angsuran(){
        return $this->hasMany('App\Models\list_angsuran');
    }

    public function lap_list_angsuran(){
        return $this->hasMany('App\Models\list_angsuran')->where([['status',0],['tgl_bayar','!=','0000-00-00']]);
    }
}
