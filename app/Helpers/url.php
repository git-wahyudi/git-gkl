<?php
function url_admin($path){
	return url('admin/'.$path);
}

function active($path){
	if($path != null){
		$id = DB::table('menus')->where('url', $path)->select('menu_id')->first();
		return $id->menu_id;
	}else {
		return 0;
	}
}