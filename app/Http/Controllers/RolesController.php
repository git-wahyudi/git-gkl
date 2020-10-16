<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateRole;
use App\Http\Requests\ValidateRoleU;
use App\Http\Requests\ValidateRoleMenu;
use App\Http\Requests\ValidateRoleMenuU;

use App\Models\role;
use App\Models\role_menu;
use App\Models\menu;
use DataTables;
use DB;

class RolesController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	return view('pengaturan.roles');
    }

    function datatable(){
        ini_set('memory_limit', '-1');
        $query = role::select('id','nama_role','uuid')->withCount('role_menu')->groupby('id')->get();
        return Datatables::of($query)
            ->addColumn('list_menu', function($query){
                $list_menu = 
                "<a href=\"".url('admin/pengaturan-roles/'.$query->uuid)."\" class=\"btn btn-xs btn-primary\">$query->role_menu_count Menu</a>";

                return $list_menu;
            })
            ->addColumn('action', function ($query) {
                $update = ""; $delete = "";
                if($this->ucu()){
                    $update = '<a href="#" data-toggle="modal" data-target="#modal-update-roles" title="edit" data-uuid="'.$query->uuid.'"><i class="fa fa-edit natural" ></i></a>';
                }
                if($this->ucd()){
                    $delete = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-delete-roles" title="hapus" data-uuid="'.$query->uuid.'"> <i class="fa fa-trash-o natural"></i> </a>';
                }
                $action = $update."&nbsp;".$delete; 
                if ($action=="&nbsp;"){$action='<a href="#" class="act"><i class="fa fa-lock natural"></i></a>'; }
                return $action;
            })
            ->addIndexColumn()
            ->rawColumns(['list_menu','action'])
            ->make(true);
    }

    function submit_data(ValidateRole $r){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        $record =[
            'nama_role'  => trim($r->roles),
            'uuid'       => $this->Uuid()
        ];

        if(role::where('nama_role', trim($r->roles))->exists() == true){
            return response()->json(['error'=> 'Data sudah ada!']);
        }

        DB::beginTransaction();
        try{
            role::create($record);
            DB::commit();
            return response()->json(['success' => 'Data barhasil ditambahkan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        } 
    }

    function get_record($uuid){
    	$data = role::where('uuid',$uuid)->firstOrFail();
    	return response()->json($data);
    }

    function update_data(ValidateRoleU $r){
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        $uuid = $r->uuidU;
        $nama_roles = trim($r->rolesU);
        $record = ['nama_role'=>$nama_roles];

        DB::beginTransaction();
        try{
            if(role::where('uuid', $uuid)->exists() == true){
                if(role::where([['nama_role',$nama_roles],['uuid','!=',$uuid]])->exists() == true){
                    return response()->json(['error'=> 'Data sudah ada!']);
                }
            }
            $role = role::where('uuid',$uuid)->firstOrFail();
            $role->update($record);
            DB::commit();
            return response()->json(['success' => 'Data barhasil diubah!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function delete_data(Request $r){
        $uuid = $r->uuidD;
        if(!$this->ucd()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        DB::beginTransaction();
        try{
            $role = role::where('uuid',$uuid)->firstOrFail();
            $role->delete();
            DB::commit();
            return response()->json(['success' => 'Data berhasil dihapus!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        } 
    }



    function index_role_menu($uuid){
        $role = role::where('uuid',$uuid)->firstOrFail();
        $list_menu = menu::select('id as value','nama_menu as text')
            ->where('url','!=','#')
            ->orderby('menu_id','asc')
            ->orderby('urutan','asc')
            ->get();
        return view('pengaturan.roles-menu', ['role'=>$role, 'list_menu'=>$list_menu]);
    }

    function deep_datatable($uuid){
        ini_set('memory_limit', '-1');
        $id = role::where('uuid',$uuid)->select('id')->firstOrFail();
        $data = role_menu::select('id','role_id','menu_id','a_create','a_update','a_delete','uuid')->with('role:id,nama_role','menu:id,nama_menu')->where('role_id',$id->id)->get('asc');
        return Datatables::of($data)
            ->addColumn('create', function($data){
                return $data->a_create==1 ? "<i class='fa fa-check-square-o natural'></i>" : "<i class='fa fa-window-close-o  natural'></i>";
            })
            ->addColumn('update', function($data){
                return $data->a_update==1 ? "<i class='fa fa-check-square-o natural'></i>" : "<i class='fa fa-window-close-o  natural'></i>";
            })
            ->addColumn('delete', function($data){
                return $data->a_delete==1 ? "<i class='fa fa-check-square-o natural'></i>" : "<i class='fa fa-window-close-o  natural'></i>";
            })
            ->addColumn('action', function($data){
                $update=""; $delete="";
                if($this->ucu()){
                    $update = '<a href="#" class="act" data-toggle="modal" data-target="#modal-edit-role-menu" data-uuid="'.$data->uuid.'" title="edit"><i class="fa fa-pencil-square-o natural"></i></a> ';
                }
                if($this->ucd()){    
                    $delete = '<a href="#" data-toggle="modal" data-target="#modal-hapus-role-menu" title="hapus" data-uuid="'.$data->uuid.'"><i class="fa fa-trash-o natural"></i></a>';
                }
                $action = $update."&nbsp;".$delete;
                if ($action=="&nbsp;"){$action='<a href="#" class="act"><i class="fa fa-lock natural"></i></a>'; }
                return $action;
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'create','update', 'delete'])
            ->make(true);
    }

    function insert_data(ValidateRoleMenu $r,$uuid){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        $id = role::where('uuid',$uuid)->firstOrFail();
        $record = array(
            "role_id"   => $id->id,
            "menu_id"   => $r->id_menu,
            "a_create"  => $r->create,
            "a_update"  => $r->update,
            "a_delete"  => $r->delete,
            "uuid"      => $this->Uuid()
        );

        if(role_menu::where([['menu_id', $r->id_menu],['role_id', $id->id]])->exists() == true){
            return response()->json(['error'=>'Role menu sudah terdaftar!']);
        }

        DB::beginTransaction();
        try {
            role_menu::create($record);
            DB::commit();            
            return response()->json(['success'=>'Data berhasil ditambahkan!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }
    }

    function get_data($uuid){
        $data = role_menu::select('id','role_id','menu_id','a_create','a_update','a_delete','uuid')->with('role:id,nama_role','menu:id,nama_menu')->where('uuid',$uuid)->firstOrFail();
        return response()->json($data);
    }

    function update_role_menu(ValidateRoleMenuU $r,$uuid){
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        $uuid_role_menu = trim($r->uuid_roleU);
        $record = array(
            "menu_id"     => $r->id_menuU,
            "a_create"    => $r->createU,
            "a_update"    => $r->updateU,
            "a_delete"    => $r->deleteU
        );

        DB::beginTransaction();
        try {
            if(role_menu::select('id','role_id','menu_id','a_create','a_update','a_delete','uuid')->with('role:id,nama_role','menu:id,nama_menu')->where('uuid',$uuid_role_menu)->exists() == true){
                $id = role::where('uuid',$uuid)->firstOrFail();
                if(role_menu::where([['menu_id', $r->id_menu],['role_id', $id->id],['uuid','!=',$uuid_role_menu]])->exists() == true){
                    return response()->json(['error'=>'Role menu sudah terdaftar!']);
                }
            }
            $rm = role_menu::where('uuid',$uuid_role_menu)->firstOrFail();
            $rm->update($record);
            DB::commit();            
            return response()->json(['success'=>'Data berhasil diubah!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }
    }

    function delete_role_menu(Request $r){
        $uuid = $r->uuid_roleD;
        if(!$this->ucd()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        DB::beginTransaction();
        try {
            $rm = role_menu::where('uuid',$uuid)->firstOrFail();
            $rm->delete();
            DB::commit();
            return response()->json(['success'=>'Data berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }
    }
}
