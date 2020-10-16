<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class list_angsuran extends Model
{
    use Uuid;
    protected $guarded 	= [];

    public function penjualan(){
    	return $this->belongsTo('App\Models\penjualan');
    }
}
