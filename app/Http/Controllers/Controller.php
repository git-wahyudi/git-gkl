<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Uuid;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function Uuid(){
    	list($usec, $sec) = explode(" ", microtime());
    	$time = ((float)$usec + (float)$sec);
    	$time = str_replace(".", "-", $time);
    	$panjang = strlen($time);
    	$sisa = substr($time, -1*($panjang-5));
    	return Uuid::generate(3,rand(100,999).rand(10,90).substr($time, 0,5).'-'.rand(0,109).rand(0,19)."-".$sisa,Uuid::NS_DNS);
    }

    // function allow_user_update(){
    function ucu(){
    	loadHelper('akses'); return ucu();
    }

   // function allow_user_delete(){
    function ucd(){
    	loadHelper('akses'); return ucd();
    }

    // function allow_user_create(){
    function ucc(){
    	loadHelper('akses'); return ucc();
    }

    function getEnumTable($table, $field){
        $type = DB::select("SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'");
        preg_match("/^enum\(\'(.*)\'\)$/", $type[0]->Type, $matches);
        $enum = explode("','", $matches[1]);
        $arr = array();
        foreach ($enum as $e){
            array_push($arr, ['value'=>$e, 'text'=>$e]);
        }
        $arr = json_decode(json_encode($arr));
        return $arr;
    }
}
