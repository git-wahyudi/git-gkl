<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateMenu;
use App\Http\Requests\ValidateMenuU;

use App\Models\menu;
use DataTables;
use DB;

class MenuController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	return view('pengaturan.menu');
    }

    function datatable(){
        // ->filter(function ($query) {
        //     if (request()->has('search')) {
        //         $search = request('search');
        //         $keyword = $search['value'];
        //         if(strlen($keyword)>=5){
        //             $query->whereRaw("a.pintu like '$keyword%' or a.tgl_kedatangan like '%$keyword%' or a.nama like '$keyword%' or a.jk like '$keyword%' or a.umur like '$keyword%' or a.no_identitas like '$keyword%' or a.no_hp like '$keyword%' or a.no_armada like '$keyword%' or a.alamat_jambi like '$keyword%' or a.kab like '$keyword%' or a.kec like '$keyword%' or a.kel like '$keyword%' or a.luar_jambi like '$keyword%' or a.asal_kedatangan like '$keyword%' or a.kondisi_kesehatan like '$keyword%' or a.status_orang like '$keyword%'");
        //         }
        //     }
        // })
        
        ini_set('memory_limit', '-1');
        $data = menu::select('id','menu_id','nama_menu','url','urutan','uuid')->with('menu_parent:id,nama_menu')->get(); 
        return Datatables::of($data)
            ->addColumn('menu_induk', function($data){
                $induk = "-";
                if($data->menu_id != 0){
                    $induk = $data->menu_parent->nama_menu;
                }
                return $induk;
            })
            ->addColumn('action', function($data){
                $update=""; $delete="";
                if($this->ucu()){
                    $update = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-update-menu" title="edit" data-uuid="'.$data->uuid.'"><i class="fa fa-pencil-square-o natural" ></i></a> ';
                }
                if($this->ucd()){
                    $delete = '&nbsp;&nbsp;<a href="#" data-toggle="modal" data-target="#modal-delete-menu" title="hapus" data-uuid="'.$data->uuid.'"><i class="fa fa-trash-o natural"></i></a>';
                }

                $action = $update."&nbsp;".$delete;
                if ($action=="&nbsp;"){$action='<a href="#" class="act"><i class="fa fa-lock natural"></i></a>'; }
                return $action;
            })
            ->addIndexColumn()
            ->rawColumns(['menu_induk', 'action'])
            ->make(true);
    }

    function getSelectValue(){
        $list_induk = menu::where('menu_id', 0)->select('id as value','nama_menu as text')->get();
        return response()->json($list_induk);
    }

    function get_record($uuid){
        $data = menu::where('uuid',$uuid)->firstOrFail();
        return response()->json($data);
    }

    function get_record_with_data($uuid){
        $data = menu::where('uuid',$uuid)->firstOrFail();
        $list_induk = menu::where('menu_id', 0)->select('id as value','nama_menu as text')->get();
        return response()->json(['data'=>$data,'list_induk'=>$list_induk]);
    }

    function submit_data(ValidateMenu $r){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }
        
        $imu = 0;
        if($r->menu_utama!=null){
            $imu = $r->menu_utama;
        }
        $record = [
            'nama_menu'     => trim($r->menu),
            'url'           => trim($r->url),
            'menu_id'       => $imu,
            'urutan'        => $r->urutan,
            'icon'          => trim($r->icon),
            'uuid'          => $this->Uuid()
        ];

        if(menu::where('nama_menu', trim($r->menu))->exists() == true){
            return response()->json(['error'=> 'Data sudah ada!']);
        }

        DB::beginTransaction();
        try{
            menu::create($record);
            DB::commit();
            return response()->json(['success' => 'Data barhasil ditambahkan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function update_data(ValidateMenuU $r){
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        $uuid = trim($r->uuidU);
        $record = [
            'nama_menu'     => trim($r->menuU),
            'url'           => trim($r->urlU),
            'menu_id'       => $r->menu_utamaU,
            'urutan'        => trim($r->urutanU),
            'icon'          => trim($r->iconU),
        ];

        DB::beginTransaction();
        try{
            if(menu::where('uuid',$uuid)->exists() == true){
                if(menu::where([['nama_menu',trim($r->menuU)],['uuid','!=',$uuid]])->exists() == true){
                    return response()->json(['error'=> 'Data sudah ada!']);
                }
            }

            $menu = menu::where('uuid',$uuid)->firstOrFail();
            $menu->update($record);
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
            $menu = menu::where('uuid',$uuid)->firstOrFail();
            $menu->delete();
            DB::commit();
            return response()->json(['success' => 'Data berhasil dihapus!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        } 
    }
}
