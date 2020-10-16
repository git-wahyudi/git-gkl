<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use DB;
use Session;

class AuthMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!Auth::check()){
            return redirect()->guest('/');
        }

        $path = $request->segment(2);

        if($path=='' || $path=='get-record' || $path=='edit-profile' || $path=='submit-password'){
            return $next($request);
        }


        $id_user = Auth::user()->id;
        $cek_menu = DB::select("
            select count(a.id) as akses, 
            sum(b.a_create) as a_create, sum(b.a_update) as a_update ,sum(b.a_delete) as a_delete 
            from menus as a, role_menus as b, user_roles as c 
            where a.id=b.menu_id and b.role_id = c.role_id 
            and a.url='$path' and c.user_id='$id_user' group by a.id
            ");
       
        if (count($cek_menu)==0){
            return redirect()->guest('/404');
        }else{
            $cek_menu = $cek_menu[0];
            $crud_akses = array('update'=>$cek_menu->a_update,
                            'create'=>$cek_menu->a_create,
                            'delete'=>$cek_menu->a_delete);
            Session::put('uc-'.$path,json_encode($crud_akses));
        }
        return $next($request);
    }
}
