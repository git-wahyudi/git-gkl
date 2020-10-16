<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateProject;
use App\Http\Requests\ValidateProjectU;

use App\Models\project;
use App\Models\project_item;
use DataTables;
use DB;

class ProjectController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	return view('master-data.project');
    }

    function datatable(){
        ini_set('memory_limit', '-1');
        $data = project::with('project_item')->select('id','project','alamat','uuid'); 
        return Datatables::of($data)
            ->filter(function ($data) {
	            if (request()->has('search')) {
	                $search = request('search');
	                $keyword = $search['value'];
	                if(strlen($keyword)>=3){
	                    $data->whereRaw("project like '%$keyword%' or alamat like '%$keyword%'");
	                }
	            }
	        })
            ->addColumn('list_project', function($data){
                $count = $data->project_item->count();
                $list_project = 
                "<a href=\"".url('admin/master-data-project/'.$data->uuid)."\" class=\"btn btn-xs btn-warning\">".$count." Unit</a>";

                return $list_project;
            })
            ->addColumn('action', function($data){
                $update=""; $delete="";
                if($this->ucu()){
                    $update = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-update-data" title="edit" data-uuid="'.$data->uuid.'"><i class="fa fa-pencil-square-o natural" ></i></a> ';
                }
                if($this->ucd()){
                    $delete = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-delete-data" title="hapus" data-uuid="'.$data->uuid.'"><i class="fa fa-trash-o natural"></i></a>';
                }

                $action = $update."&nbsp;".$delete;
                if ($action=="&nbsp;"){$action='<a href="#" class="act"><i class="ti ti-lock natural"></i></a>'; }
                return $action;
            })
            ->addIndexColumn()
            ->rawColumns(['action','list_project'])
            ->make(true);
    }

    function get_record($uuid){
        $data = project::whereUuid($uuid)->firstOrFail();
        return response()->json($data);
    }

    function submit_data(ValidateProject $r){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
            $p = new project();
            $p->project     = trim($r->project);
            $p->alamat      = trim($r->alamat);
            $p->save();

            DB::commit();
            return response()->json(['success' => 'Data barhasil ditambahkan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function update_data(ValidateProjectU $r){
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
            $uuid = trim($r->uuidU);
            $up = project::whereUuid($uuid)->firstOrFail();
            $up->project = trim($r->projectU);
            $up->alamat = trim($r->alamatU);
            $up->save();

            DB::commit();
            return response()->json(['success' => 'Data barhasil diubah!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function delete_data(Request $r){
        if(!$this->ucd()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        DB::beginTransaction();
        try{
            $uuid = trim($r->uuidD);
            $dp = project::whereUuid($uuid)->firstOrFail();
            if(project_item::whereProject_id($dp->id)->exists()){
                return response()->json(['error' => 'Data telah digunakan!']);
            }

            $dp->delete();
            
            DB::commit();
            return response()->json(['success' => 'Data berhasil dihapus!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        } 
    }
}
