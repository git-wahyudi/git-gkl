<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\penjualan;
use DataTables;
use DB;

class LunasController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	return view('transaksi.angsuran.lunas');
    }

    function datatable(){
        ini_set('memory_limit', '-1');
      	
      	$param = (!empty($_GET["param"])) ? ($_GET["param"]) : ('');
        $data = penjualan::where([['status',2],['is_lunas',1],['no_transaksi','like','%'.$param.'%']])->whereIn('tipe',['Kredit','Cash Tempo'])->orWhere([['status',2],['is_lunas',1],['nama','like','%'.$param.'%']])->whereIn('tipe',['Kredit','Cash Tempo']);
        return Datatables::of($data)
            ->addColumn('total', function($data){
                return $data->total_harga-($data->potongan+$data->uang_muka);
            })
            ->addColumn('detail', function($data){
                
                $detail = 
                "<a href=\"".url('admin/history-kredit'.'/'.$data->uuid)."\" class=\"btn btn-xs btn-warning\">Detail</a>";

                return $detail;
            })
            ->addIndexColumn()
            ->rawColumns(['total','jml_bayar','detail'])
            ->make(true);
    }
}
