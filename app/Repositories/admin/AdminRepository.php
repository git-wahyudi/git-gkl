<?php
namespace App\Repositories\admin;

use App\Models\user;

class AdminRepository
{
	function find($id){
        return user::find($id);
   	}

    function update(array $data, $id){
        $record = user::where('id',$id)->firstOrFail();
        return $record->update($data);
    }

    function update_profile(array $data, $id){
        $record = user::where('id',$id)->firstOrFail();
        return $record->update($data);
    }

    function update_photo(array $data, $id){
        $record = user::where('id',$id)->firstOrFail();
        return $record->update($data);
    }
}