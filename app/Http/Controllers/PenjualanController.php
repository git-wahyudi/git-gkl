<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidatePenjualan;
use App\Http\Requests\ValidatePenjualanU;

use App\Models\project;
use App\Models\customer;
use App\Models\penjualan;
use App\Models\penjualan_detail;
use App\Models\project_item;
use App\Models\owner;
use DataTables;
use DateTime;
use DB;

class PenjualanController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	$c = customer::select('id as value',DB::raw("CONCAT(nama,' - ',nik) as text"))->get();
        $p = project::select('id as value','project as text')->get();
    	$o = owner::select('id as value','nama as text')->get();
    	return view('transaksi.penjualan.index', compact('c','p','o'));
    }

    function datatable(){
        ini_set('memory_limit', '-1');
        $data = DB::table('penjualans')->select('id','no_transaksi','nama','project','tgl_penjualan','tipe','status','uuid')->orderBy('status','asc')->orderBy('tgl_penjualan','desc');
        return Datatables::of($data)
            ->filter(function ($data) {
	            if (request()->has('search')) {
	                $search = request('search');
	                $keyword = $search['value'];
	                if(strlen($keyword)>=3){
	                    $data->whereRaw("nama like '%$keyword%' or no_transaksi like '%$keyword%' or project like '%$keyword%'");
	                }
	            }
	        })
            ->addColumn('tgl', function($data){
                return toDateDisplay($data->tgl_penjualan);
            })
            ->addColumn('status', function($data){
                if($data->status == 1){
                    $status ="<span class=\"badge badge-danger \">On Process</span>";
                }else if($data->status == 2){
                    $status ="<span class=\"badge badge-success \">Completed</span>";
                }

                return $status;
            })
            ->addColumn('detail', function($data){
                
                $detail = 
                "<a href=\"".url('admin/penjualan/'.strtolower(str_replace(" ", "-", $data->tipe)).'/'.$data->uuid)."\" class=\"btn btn-xs btn-warning\">Detail</a>";

                return $detail;
            })
            ->addColumn('action', function($data){
                if($data->status == 2 || $data->status == 3){
                    $action='<a href="#" class="act"><i class="ti ti-lock natural"></i></a>';
                }else { 
                    $update=""; $delete="";
                    if($this->ucu()){
                        $update = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-update-data" title="edit" data-uuid="'.$data->uuid.'"><i class="fa fa-pencil-square-o natural" ></i></a> ';
                    }
                    if($this->ucd()){
                        $delete = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-delete-data" title="hapus" data-uuid="'.$data->uuid.'"><i class="fa fa-trash-o natural"></i></a>';
                    }

                    $action = $update."&nbsp;".$delete;
                    if ($action=="&nbsp;"){$action='<a href="#" class="act"><i class="ti ti-lock natural"></i></a>'; }
                }
                return $action;
            })
            ->addIndexColumn()
            ->rawColumns(['action','status','status','tgl','detail'])
            ->make(true);
    }

    function get_record($uuid){
        $data = penjualan::whereUuid($uuid)->firstOrFail();
        return response()->json($data);
    }

    function submit_data(ValidatePenjualan $r){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
            $c = customer::find(trim($r->konsumen));
            $p = project::find(trim($r->project));
            $o = owner::find(trim($r->penjual));

            $pjl = new penjualan();
            $pjl->owner_id          = $o->id;
            $pjl->o_nama            = $o->nama;
            $pjl->o_nik             = $o->nik;
            $pjl->o_ttl             = $o->ttl;
            $pjl->o_jk              = $o->jk;
            $pjl->o_alamat          = $o->alamat;
            $pjl->o_agama           = $o->agama;
            $pjl->o_pekerjaan       = $o->pekerjaan;
            $pjl->o_telp            = $o->telp;
            $pjl->customer_id       = $c->id;
            $pjl->nama              = $c->nama;
            $pjl->nik               = $c->nik;
            $pjl->ttl               = $c->ttl;
            $pjl->jk                = $c->jk;
            $pjl->alamat            = $c->alamat;
            $pjl->agama             = $c->agama;
            $pjl->pekerjaan         = $c->pekerjaan;
            $pjl->telp              = $c->telp;
            $pjl->project_id        = $p->id;
            $pjl->project           = $p->project;
            $pjl->project_alamat    = $p->alamat;
            $pjl->tgl_penjualan     = trim(toDateSystem($r->tanggal));
            $pjl->status            = 1;
            $pjl->tipe              = $r->tipe;
            $pjl->saksi_pertama     = trim($r->saksi1);
            $pjl->saksi_kedua       = trim($r->saksi2);
            $pjl->save();

            DB::commit();
            return response()->json(['success' => 'Data barhasil ditambahkan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function update_data(ValidatePenjualanU $r){
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
            $uuid = trim($r->uuidU);
            $c = customer::find(trim($r->konsumenU));
            $p = project::find(trim($r->projectU));
            $o = owner::find(trim($r->penjualU));

            $pjl = penjualan::with('penjualan_detail')->where('uuid',$uuid)->firstOrFail();

            if($p->id != $pjl->project_id && $pjl->penjualan_detail->count() > 0){
                return response()->json(['error' => 'Untuk merubah project silahkan hapus terlebih dahulu detail kavling!']);
            }

            //cek sebelum update data
            if($pjl->status == 2 || $pjl->status == 3){
                return response()->json(['error' => 'Data tidak bisa diubah!']);
            }

            $pjl->owner_id          = $o->id;
            $pjl->o_nama            = $o->nama;
            $pjl->o_nik             = $o->nik;
            $pjl->o_ttl             = $o->ttl;
            $pjl->o_jk              = $o->jk;
            $pjl->o_alamat          = $o->alamat;
            $pjl->o_agama           = $o->agama;
            $pjl->o_pekerjaan       = $o->pekerjaan;
            $pjl->o_telp            = $o->telp;
            $pjl->customer_id       = $c->id;
            $pjl->nama              = $c->nama;
            $pjl->nik               = $c->nik;
            $pjl->ttl               = $c->ttl;
            $pjl->jk                = $c->jk;
            $pjl->alamat            = $c->alamat;
            $pjl->agama             = $c->agama;
            $pjl->pekerjaan         = $c->pekerjaan;
            $pjl->telp              = $c->telp;
            $pjl->project_id        = $p->id;
            $pjl->project           = $p->project;
            $pjl->project_alamat    = $p->alamat;
            $pjl->tgl_penjualan     = trim(toDateSystem($r->tanggalU));
            $pjl->tipe              = $r->tipeU;
            $pjl->saksi_pertama     = trim($r->saksi1U);
            $pjl->saksi_kedua       = trim($r->saksi2U);
            $pjl->save();


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
            $pjl = penjualan::whereUuid($uuid)->firstOrFail();
            if($pjl->status == 2 || $pjl->status == 3){
                return response()->json(['error' => 'Data tidak bisa dihapus!']);
            }

            $pjl->delete();
            $pd = penjualan_detail::wherePenjualan_id($pjl->id)->get();
            foreach ($pd as $p) {
                $pi = project_item::find($p->project_item_id);
                $pi->status = 0;
                $pi->save();

                //hapus penjualan detail item
                $p->delete();
            }
            
            DB::commit();
            return response()->json(['success' => 'Data berhasil dihapus!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        } 
    }
}
