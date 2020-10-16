<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateCustomer;
use App\Http\Requests\ValidateCustomerU;

use App\Models\customer;
use DataTables;
use DB;

class CustomerController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	return view('master-data.customer');
    }

    function datatable(){
        ini_set('memory_limit', '-1');
        $data = DB::table('customers')->select('id','nama','nik','alamat','telp','uuid'); 
        return Datatables::of($data)
            ->filter(function ($data) {
	            if (request()->has('search')) {
	                $search = request('search');
	                $keyword = $search['value'];
	                if(strlen($keyword)>=3){
	                    $data->whereRaw("nm_customer like '%$keyword%' or alamat like '%$keyword%' or telp like '%$keyword%'");
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

    function get_record($uuid){
        $data = customer::where('uuid',$uuid)->firstOrFail();
        return response()->json($data);
    }

    function submit_data(ValidateCustomer $r){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
            $cs = new customer();
            $cs->nama       = trim($r->nama);
            $cs->nik        = trim($r->nik);
            $cs->ttl        = trim($r->ttl);
            $cs->jk         = trim($r->jk);
            $cs->alamat     = trim($r->alamat);
            $cs->agama      = trim($r->agama);
            $cs->pekerjaan  = trim($r->pekerjaan);
            $cs->telp       = trim($r->telp);
            $cs->save();

            DB::commit();
            return response()->json(['success' => 'Data barhasil ditambahkan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function update_data(ValidateCustomerU $r){
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
            $uuid = trim($r->uuidU);
            $cs = customer::whereUuid($uuid)->firstOrFail();
            $cs->nama       = trim($r->namaU);
            $cs->nik        = trim($r->nikU);
            $cs->ttl        = trim($r->ttlU);
            $cs->jk         = trim($r->jkU);
            $cs->alamat     = trim($r->alamatU);
            $cs->agama      = trim($r->agamaU);
            $cs->pekerjaan  = trim($r->pekerjaanU);
            $cs->telp       = trim($r->telpU);
            $cs->save();
            
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

        $uuid = trim($r->uuidD);
        DB::beginTransaction();
        try{
            customer::where('uuid',$uuid)->delete();
            DB::commit();
            return response()->json(['success' => 'Data berhasil dihapus!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        } 
    }
}
