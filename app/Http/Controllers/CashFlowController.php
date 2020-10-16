<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateCashFlow;

use App\Models\biaya;
use DataTables;
use DateTime;
use DB;

class CashFlowController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	return view('transaksi.cash-flow.index');
    }

    function datatable(){
        ini_set('memory_limit', '-1');
        $data = DB::select('SELECT id,no_transaksi,tgl,code,keterangan,jumlah,status,uuid,
                   SUM(jumlah) OVER(ORDER BY tgl ASC, id ASC
                                ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) 
                                AS balance
                FROM biayas ORDER BY tgl DESC, id DESC');
        return Datatables::of($data)
            ->addColumn('tanggal', function($data){
                return toDateDisplay($data->tgl);
            })
            ->addColumn('in', function($data){
            	$in = '-';
            	if($data->code == 'In')
            		$in = $data->jumlah;
                return $in;
            })
            ->addColumn('out', function($data){
            	$out = '-';
            	if($data->code == 'Out')
            		$out = str_replace('-', '', $data->jumlah);
                return $out;
            })
            ->addColumn('detail', function($data){
                
                $detail = '<a type="button" class="btn btn-warning text-white btn-xs" data-toggle="modal" data-target="#modal-detail" data-uuid="'.$data->uuid.'">Info</a>';

                return $detail;
            })
            ->addIndexColumn()
            ->rawColumns(['tanggal','detail'])
            ->make(true);
    }

    function get_detail($uuid){
        $data = biaya::whereUuid($uuid)->firstOrFail();
        $ca = datetime($data->created_at);
        return response()->json(['cb'=>$data->created_by, 'ca'=>$ca]);
    }

    function submit_data(ValidateCashFlow $r){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
        	//create no transaksi
            $number = biaya::count('id');
            $number = $number+1;
            $no = 'TRANS-'.toZero(4,$number);

            if($r->code == 'In'){
                $jumlah = toMoneyInput(trim($r->jumlah));
            }else if($r->code == 'Out'){
                $jumlah = '-'.toMoneyInput(trim($r->jumlah));
            }

            $b = new biaya();
            $b->no_transaksi	= $no;
            $b->tgl 			= toDateSystem($r->tanggal);
            $b->code 			= $r->code;
            $b->keterangan 		= trim($r->ket);
            $b->jumlah 			= $jumlah;
            $b->status 			= 1;
            $b->save();

            DB::commit();
            return response()->json(['success' => 'Data barhasil ditambahkan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }
}
