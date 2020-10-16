<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\project;
use Dompdf\Dompdf;
use DateTime;
use Auth;
use DB;

class LaporanAngsuranController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index(){
        $p = project::get();
        return view('laporan.angsuran',compact('p'));
    }

    function get_laporan($project){

        DB::beginTransaction();
        try{
            $data = project::with(['lap_penjualan.penjualan_detail', 'lap_penjualan.lap_list_angsuran'])->whereId($project)->firstOrfail();

            $html = view('laporan/lap-ang',compact('data'));
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            // $dompdf->setPaper(array(0,0,609.4488,935.433), 'Potrait');
            // $dompdf->setPaper(array(0,0,595,276,420,94488), 'Potrait');
            $dompdf->setPaper('A4', 'Landscape');
            
            $dompdf->render();
            $canvas = $dompdf ->get_canvas();
            // $canvas->page_text(525, 255, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
            // $canvas->page_text(23, 255, "Print at : ".$date." by ".ucwords(Auth::user()->nama), null, 8, array(0, 0, 0));
            $dompdf->stream('laporan-angsuran.pdf', array("Attachment" => false));
            DB::commit();
        }catch (\Exception $e){
            dd($e);
            DB::rollback();
            echo "<script>window.close();</script>";
        }
    }

}
