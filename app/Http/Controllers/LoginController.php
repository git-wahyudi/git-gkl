<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;	
use Auth;
use Session;
use Hash;
use File;
use App\Models\User;
use Validator;

class LoginController extends Controller
{
    function submit_login(Request $r){
        if(Auth::user()){
            return redirect('/admin');
        }

        $validator = Validator::make($r->all(),[
        	'key'	=> 'required|min:6|max:30',
        	'value'	=> 'required|min:6|max:30'
        ]);
        if($validator->fails()){
        	return redirect('/')->with('error', 'Data login tidak valid!');
        }

        $username = trim($r->key);
        $password = trim($r->value);

        if(Auth::Attempt(['username' => $username, 'password' => $password])){
            $user = user::where('username', $username)->select('id', 'status')->first();
            if($user->status == 0){
                Auth::logout();
                Session::flush();
                return redirect('/')->with('error', 'User anda sedang di non-aktifkan!');
            }else{
                $this->usermenu();
                return redirect('/admin')->with('success', 'Selamat datang!');
            }
        }else {
            return redirect('/')->with('error', 'Username dan password tidak ditemukan!');
        }
    }

    function logout(){
    	Auth::logout();
    	Session::flush();
    	return redirect('/');
    }

    function usermenu(){
        $id_user = Auth::user()->id;
        $menu_user = array();
        
        $menu_induk = DB::select("select d.*
                            from user_roles as a, 
                            role_menus as b, 
                            menus as c , 
                            menus as d
                            where a.role_id = b.role_id 
                            and c.id = b.menu_id  
                            and c.menu_id = d.id
                            and a.user_id = $id_user
                            group by d.id order by d.urutan");
        foreach($menu_induk as $mni){
            $menu_user[$mni->id]['id_menu'] = $mni->id;
            $menu_user[$mni->id]['url'] = $mni->url;
            $menu_user[$mni->id]['icon'] = $mni->icon;
            $menu_user[$mni->id]['nama_menu'] = $mni->nama_menu;

                    $id_menu_induk = $mni->id;

                    $menu_anak = DB::select("select c.nama_menu, c.id, c.url, c.urutan, c.menu_id, c.icon, b.a_create, b.a_update, b.a_delete from 
                        user_roles as a, 
                        role_menus as b, 
                        menus as c , 
                        menus as d 
                        where a.role_id = b.role_id 
                        and c.id = b.menu_id 
                        and c.menu_id = d.id 
                        and a.user_id = $id_user 
                        and c.menu_id=$id_menu_induk 
                        group by c.nama_menu, c.id, c.url, c.urutan, c.menu_id, b.a_create, b.a_update, b.a_delete
                        order by c.menu_id, c.urutan ");

                    $temp_anak = array();
                    foreach($menu_anak as $mna){
                        array_push($temp_anak, array("id_menu"=>$mna->id, "url"=>$mna->url, "nama_menu"=>$mna->nama_menu, "icon"=>$mna->icon));
                    }
            $menu_user[$mni->id]['child'] = $temp_anak;
        }   
        $menu_user = json_encode($menu_user);
        Session::put('menu_session',$menu_user);
    }
}
