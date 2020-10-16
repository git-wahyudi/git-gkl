<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class owner extends Model
{
    use Uuid;
    protected $guarded 	= [];
}
