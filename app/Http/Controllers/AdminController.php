<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidatePassword;
use App\Http\Requests\ValidateProfile;
use App\Repositories\admin\AdminService;
use App\Repositories\admin\AdminRepository;
use App\Models\project;
use App\Models\project_item;
use App\Models\User;
use DataTables;
use Auth;
use DB;
use Hash;
use File;

class AdminController extends Controller
{
    protected $service;
    protected $repo;
    public function __construct(AdminService $service, AdminRepository $repo){
       $this->service = $service;
       $this->repo = $repo;
    }

    function index(){
        $user = user::with('user_role')->find(Auth::user()->id);
        if($user->user_role->role_id == 3){
            $p = project::select('id as value','project as text')->get();
            return view('sales.index', compact('p'));
        }
    	return view('main');
    }

    function datatable($id){
        ini_set('memory_limit', '-1');
        $data = project::with('project_item')->whereId($id);
        return Datatables::of($data)
            ->addColumn('data', function($data){
                $isi ="<div class='col text-center'>Tidak ada data.</div>";
                if($data->exists() && $data->project_item->count() > 0){
                    $isi = "";
                    foreach ($data->project_item as $pi) {
                        if($pi->status == 0){
                            $p = '<a href="#" class="btn btn-success m-1" data-toggle="modal" data-target="#modal-kavling" data-uuid="'.$pi->uuid.'">'.$pi->no_kavling.'</a>';
                        }else if($pi->status == 1){
                            $p = '<a href="#" class="btn btn-danger m-1" data-toggle="modal" data-target="#modal-kavling" data-uuid="'.$pi->uuid.'">'.$pi->no_kavling.'</a>';
                        }
                        $isi .= $p;
                        
                    }
                }

                $show=  '<div class="badge badge-warning d-flex align-content-sm-center">Project '.$data->project.'</div>
                         <div class="row pl-3 pr-3 d-flex flex-wrap text-center">'.$isi.'</div>';
                return $show;
            })
            ->addIndexColumn()
            ->rawColumns(['data'])
            ->make(true);
    }

    function get_kavling($uuid){
        $data = project_item::whereUuid($uuid)->firstOrfail();
        return response()->json($data);
    }

    function get_record($id){
        $data = $this->repo->find(decrypt($id));
        return response()->json($data);
    }

    function submit_password(ValidatePassword $r){
        return $this->service->submit_password($r);
    }

    function edit_profile(ValidateProfile $r){
        return $this->service->edit_profile($r);
    }
}
