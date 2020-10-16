<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Auth;

class project_item extends Model
{
    use Uuid;
    protected $guarded 	= [];
}
