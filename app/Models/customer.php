<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class customer extends Model
{
    use Uuid;
    protected $guarded 	= [];
}
