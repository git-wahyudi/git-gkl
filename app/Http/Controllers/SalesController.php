<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\penjualan;
use App\Models\user;
use DataTables;
use Auth;
use DB;

class SalesController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	$p = project::select('id as value','project as text')->get();
        return view('sales.index', compact('p'));
    }

    function datatable($tgl1, $tgl2, $kasir){
    	$tgl1 = toDateSystem($tgl1);
    	$tgl2 = toDateSystem($tgl2);
        ini_set('memory_limit', '-1');
        $data = penjualan::with('pd','penjualan_retur', 'payment', 'user')->where([['posting',1],['user_id',$kasir]])->whereBetween('tgl',[$tgl1,$tgl2])->orderBy('id');
        return Datatables::of($data)
            ->addColumn('tgl', function($data){
                return toDateDisplay($data->tgl);
            })
            ->addColumn('grand_total', function($data){
                return ceil($data->grand_total);
            })
            ->addColumn('retur', function($data){
                 return $data->penjualan_retur->sum('total');
            })
            ->addColumn('gt', function($data){
                 return ceil($data->grand_total-$data->penjualan_retur->sum('total'));
            })
            ->addIndexColumn()
            ->rawColumns(['tgl','grand_total','retur'])
            ->make(true);
    }

}
