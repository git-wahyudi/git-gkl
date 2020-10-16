<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\penjualan;
use App\Models\project_item;
use App\Models\penjualan_detail;
use App\Models\list_angsuran;
use Carbon\Carbon;
use DataTables;
use WordTemplate;
use DB;
use Dompdf\Dompdf;
use Auth;

class LunasDetailController extends Controller
{
    public function __construct(){
       loadHelper('format,url');
    }

    function index($uuid){
    	$p = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();
        return view('transaksi.angsuran.lunas-detail', compact('p'));
    }

    function datatable($uuid){
        ini_set('memory_limit', '-1');
        $data = penjualan::with('list_angsuran')->whereUuid($uuid);
        return Datatables::of($data)
            ->addColumn('data', function($data){
            	$isi ="<tr><td colspan='8' class='text-center' style='border-bottom:1px solid #cdcdcd;'>Tidak ada data.</td></tr>";
            	if($data->list_angsuran->count() > 0){
            		$i = 0;
            		$isi = "";
            		$netto = $data->total_harga-($data->potongan+$data->uang_muka);
	            	foreach ($data->list_angsuran as $ang) {
	            		$i = $i+1;
	            		$netto = $netto-($ang->jml_bayar+$ang->potongan);
	            		$print = "";
	            		$catatan = "<span class=\"badge badge-danger \">Menunggak</span>";
	            		if($ang->status == 1){
		                	$print = '<a href="'.url_admin('history-kredit').'/'.$data->uuid.'/'.$ang->uuid.'" title="print kwitansi" target="__blank"><i class="fa fa-print natural"></i></a>';
		                	$catatan = $ang->catatan;
	            		}
		                
						$isi .= "<tr>
								<td class='text-center'>".$i."</td>
								<td>".$ang->ket."</td>
								<td>".$catatan."</td>
								<td>".toDateDisplay($ang->tgl_bayar)."</td>
								<td>".$ang->cara_bayar."</td>
                                <td class='text-right'>".toMoney($ang->potongan)."</td>
                                <td class='text-right'>".toMoney($ang->jml_bayar)."</td>
                                <td class='text-right'>".toMoney($netto)."</td>
								<td>".$print."</td>
							</tr>";
						
					}
				}

                $kredit='<table id="table" class="table dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
							<tbody>
					  			<tr style="background: #e9ecef;">
									<td style="width:10px;">No</td>
									<td style="width:150px;">Keterangan</td>
									<td>Catatan</td>
									<td style="width: 100px;">Tanggal</td>
									<td style="width: 100px;">Cara Bayar</td>
                                    <td class="text-right" style="width: 100px;">Potongan</td>
									<td class="text-right" style="width: 100px;">Nilai Bayar</td>
									<td class="text-right" style="width: 100px;">Sisa Piutang</td>
									<td style="width:5px;""></td>
								</tr>
								'.$isi.'
							</tbody>
						</table>';
                return $kredit;
            })
            ->addIndexColumn()
            ->rawColumns(['data'])
            ->make(true);
    }

    function print($uuid, $ang_uuid){
        DB::beginTransaction();
        try{
        	$la = list_angsuran::with('penjualan.penjualan_detail')->whereUuid($ang_uuid)->firstOrFail();
        	$i = 0;
            $kavling ='';
            foreach ($la->penjualan->penjualan_detail as $pd) {
                $kavling .= $i == 0 ? $pd->no_kavling." (".toMoney($pd->luas).' m2)' : " | ".$pd->no_kavling." (".toMoney($pd->luas).' m2)';

                $i++;
            }

            $date = Date('d-m-Y h:i:s');

            $html = view('transaksi/angsuran/kwitansi',compact('la','kavling'));
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            // $dompdf->setPaper(array(0,0,609.4488,935.433), 'Potrait');
            $dompdf->setPaper(array(0,0,595,276,420,94488), 'Potrait');
            // $dompdf->setPaper('A4', 'Potrait');
            $dompdf->render();
            $canvas = $dompdf ->get_canvas();
            // $canvas->page_text(525, 255, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
            $canvas->page_text(23, 255, "Print at : ".$date." by ".ucwords(Auth::user()->nama), null, 8, array(0, 0, 0));
            $dompdf->stream('print-kwitansi.pdf', array("Attachment" => false));

            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            echo "<script>window.close();</script>";
        }
    }
}
