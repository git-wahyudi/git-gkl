<?php
//user_can_update
function ucu(){
	$path = Request::segment(2);
	$session_akses = session('uc'.'-'.$path);
	if($session_akses){
		$session_akses = json_decode($session_akses);
		if((int)$session_akses->update>=1) return true;
	}
	return false;	
}

//user_can_create
function ucc(){
	$path = Request::segment(2);
	$session_akses = session('uc'.'-'.$path);
	if($session_akses){
		$session_akses = json_decode($session_akses);
		if((int)$session_akses->create>=1) return true;
	}
	return false;
}

//user_can_delete
function ucd(){
	$path = Request::segment(2);
	$session_akses = session('uc'.'-'.$path);
	if($session_akses){
		$session_akses = json_decode($session_akses);
		if((int)$session_akses->delete>=1) return true;
	}
	return false;
}