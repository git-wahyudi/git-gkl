<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateCreatePengguna;
use App\Http\Requests\ValidateUpdatePengguna;

use App\Models\role;
use App\Models\User;
use App\Models\user_role;
use DataTables;
use Hash;
use DB;

class PenggunaController extends Controller
{

    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	$role = role::select('uuid as value','nama_role as text')->where('id','>',1)->get();
    	return view('pengaturan.pengguna', compact('role'));
    }

    function datatable(){
        ini_set('memory_limit', '-1');
        $query = user::select('id','username','password', 'nama','status', 'uuid')->with('user_role.role')->where('id', '!=', 1)->orderBy('username','asc')->get();
        return Datatables::of($query)
            ->addColumn('status', function($query){
                $status = "";
                if($query->status == 1){
                    $status = '<i class="fa fa-check-square-o"></i> Enable';
                }
                if($query->status == 0){
                    $status = '<i class="fa fa-window-close-o"></i> Disable';
                }
                return $status;
            })
            ->addColumn('role', function($query){
                $role = "<span class='badge badge-danger m-r-5 m-b-5'>".$query->user_role->role->nama_role."</span>";
                return $role;
            })
            ->addColumn('action', function ($query) {  
                $update = ""; $delete = "";
                if($this->ucu()){
                    $update = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-update-users" title="edit" data-uuid="'.$query->uuid.'"><i class="fa fa-pencil-square-o natural"></i></a>';
                    $reset = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-reset-password" title="reset password" data-uuid="'.$query->uuid.'"><i class="fa fa-retweet natural"></i></a>';
                }
                if($this->ucd()){
                    $delete = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-delete-users" title="hapus" data-uuid="'.$query->uuid.'"><i class="fa fa-trash-o natural"></i></a>';
                }
                
                $action = $update."&nbsp;".$reset."&nbsp;".$delete; 
                if ($action=="&nbsp;"){$action='<a href="#" class="act"><i class="fa fa-lock natural"></i></a>'; }
                return $action;
            })
            ->addIndexColumn()
            ->rawColumns(['action','instansi','status','role','ttd'])
            ->make(true);
    }

    function submit_data(ValidateCreatePengguna $r){
    	if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        $role = role::where('uuid',$r->role)->firstOrFail();
        $uuid = $this->Uuid();

        $record     = [
                        'username'      => trim($r->username),
                        'password'      => Hash::make($r->password),
                        'nama'          => trim($r->nama),
                        'status'        => trim($r->status),
                        'uuid'          => $uuid
                      ];
        $user = new user();
        $user->username      = trim($r->username);
        $user->password      = Hash::make($r->password);
        $user->nama          = trim($r->nama);
        $user->status        = trim($r->status);
        
        if(user::where('username', trim($r->username))->exists() == true){
            return response()->json(['error'=> 'Username sudah digunakan!']);
        }

        DB::beginTransaction();
        try {
            $user->save();
            $data = [
                    "user_id"       => $user->id,
                    "role_id"       => $role->id,
                    "uuid"          => $this->Uuid()
                  ];
            user_role::create($data);
            DB::commit();            
            return response()->json(['success'=>'Data berhasil ditambahkan!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }
    }

    function get_record($uuid){
    	$data = user::with('user_role.role')->where('uuid',$uuid)->firstOrFail();
    	return response()->json($data);
    }

    function update_data(ValidateUpdatePengguna $r){
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        $uuid = $r->uuidU;
        $role = role::where('uuid',$r->roleU)->firstOrFail();
        $record = [
                    'nama'          => trim($r->namaU),
                    'status'        => trim($r->statusU)
                  ];
     
        DB::beginTransaction();
        try {
            //update_role
            $user = user::where('uuid',$uuid)->firstOrFail();
            $user->update($record);
            $id_user = user::with('user_role.role')->where('uuid',$uuid)->firstOrFail();

            //update user_role
            $data = [
                    "role_id"       => $role->id
                  ];
            $ur = user_role::where('user_id',$id_user->id)->firstOrFail();
            $ur->update($data);
            DB::commit();
            return response()->json(['success'=>'Data berhasil diubah!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }
    }

    function delete_data(Request $r){
        $uuid = $r->uuidD;
        if(!$this->ucd()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        $id_user = user::with('user_role.role')->where('uuid',$uuid)->firstOrFail();
        DB::beginTransaction();
        try {
            //delete role
            $role = user_role::where('user_id',$id_user->id)->firstOrFail();
            $role->delete();

            //delete user
            $user = user::where('uuid',$uuid)->firstOrFail();
            $user->delete();
            DB::commit();
            return response()->json(['success'=>'Data berhasil dihapus!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }
    }

    function reset_password(Request $r){
        $uuid = $r->uuidR;
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        $password = Hash::make('admin123');

        try {
            $user = user::where('uuid',$uuid)->firstOrFail();
            $user->update(['password'=>$password]);
            DB::commit();
            return response()->json(['success'=>'Password berhasil direset!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan!']);
        }
    }
}
