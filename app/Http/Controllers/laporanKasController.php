<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\biaya;
use App\Models\project;
use Dompdf\Dompdf;
use DateTime;
use Auth;
use DB;

class LaporanKasController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
    	$y = date('Y');
        return view('laporan.kas',compact('y'));
    }

    function get_laporan($bulan, $tahun, $ukuran){

		DB::beginTransaction();
        try{
	    	$a_date = $tahun."-".$bulan."-01";
			$date = new DateTime($a_date);
			$date->modify('last day of this month');
			$akhir = $date->format('Y-m-d');
            $date = Date('d-m-Y h:i:s');
            $periode = strtoupper(bulan($bulan)).' '.$tahun;

            $data = biaya::with('project')->whereBetween('tgl',[$a_date,$akhir])->where('status',1)->orderBy('tgl')->get();
            $header = biaya::with('project')->whereBetween('tgl',[$a_date,$akhir])->where([['project_id','>',0],['status',1]])->groupBy('project_id')->get();

            $html = view('laporan/lap-kas',compact('periode','data','header'));
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            // $dompdf->setPaper(array(0,0,609.4488,935.433), 'Potrait');
            // $dompdf->setPaper(array(0,0,595,276,420,94488), 'Potrait');
            if($ukuran == 'Potrait'){
            	$dompdf->setPaper('A4', 'Potrait');
            }else {
            	$dompdf->setPaper('A4', 'Landscape');
            }
            $dompdf->render();
            $canvas = $dompdf ->get_canvas();
            // $canvas->page_text(525, 255, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
            // $canvas->page_text(23, 255, "Print at : ".$date." by ".ucwords(Auth::user()->nama), null, 8, array(0, 0, 0));
            $dompdf->stream('laporan-kas.pdf', array("Attachment" => false));
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            echo "<script>window.close();</script>";
        }
    }

}
