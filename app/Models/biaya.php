<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class biaya extends Model
{
    use Uuid;
    protected $guarded 	= [];

    public function project(){
    	return $this->belongsTo('App\Models\project');
    }
}
