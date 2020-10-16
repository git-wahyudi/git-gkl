<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateProjectItem;
use App\Http\Requests\ValidateProjectItemU;

use App\Models\project;
use App\Models\project_item;
use DataTables;
use DB;

class ProjectItemController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index($uuid){
    	$p = project::whereUuid($uuid)->firstOrFail();
    	return view('master-data.projectItem', compact('p'));
    }

    function datatable($uuid){
        ini_set('memory_limit', '-1');
        $p = project::whereUuid($uuid)->firstOrFail();
        $data = DB::table('project_items')->select('id','project_id','no_kavling','luas','harga','total_harga','uuid')->whereProject_id($p->id);
        return Datatables::of($data)
            ->filter(function ($data) {
	            if (request()->has('search')) {
	                $search = request('search');
	                $keyword = $search['value'];
	                if(strlen($keyword)>=3){
	                    $data->whereRaw("no_kavling like '%$keyword%'");
	                }
	            }
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
            ->rawColumns(['action'])
            ->make(true);
    }

    function get_record($uuid, $detail){
        $data = project_item::whereUuid($detail)->firstOrFail();
        return response()->json($data);
    }

    function submit_data(ValidateProjectItem $r){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
        	$p = project::whereUuid(trim($r->uuidP))->firstOrFail();
        	$total = intval(toMoneyInput(trim($r->luas)))*intval(toMoneyInput(trim($r->harga)));
            $pi = new project_item();
            $pi->project_id     = $p->id;
            $pi->no_kavling     = trim($r->no);
            $pi->luas 			= intval(toMoneyInput(trim($r->luas)));
            $pi->harga 			= intval(toMoneyInput(trim($r->harga)));
            $pi->total_harga 	= $total;
            $pi->save();

            DB::commit();
            return response()->json(['success' => 'Data barhasil ditambahkan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function update_data(ValidateProjectItemU $r){
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
            $uuid = trim($r->uuidU);
            $total = intval(toMoneyInput(trim($r->luasU)))*intval(toMoneyInput(trim($r->hargaU)));

            $pi = project_item::whereUuid($uuid)->firstOrFail();
            $pi->no_kavling     = trim($r->noU);
            $pi->luas 			= intval(toMoneyInput(trim($r->luasU)));
            $pi->harga 			= intval(toMoneyInput(trim($r->hargaU)));
            $pi->total_harga 	= $total;
            $pi->save();

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
            $pi = project_item::whereUuid($uuid)->firstOrFail();
            // if(project_list::whereProject_id($dp->id)->exists()){
            //     return response()->json(['error' => 'Data telah digunakan!']);
            // }

            $pi->delete();
            
            DB::commit();
            return response()->json(['success' => 'Data berhasil dihapus!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        } 
    }
}
