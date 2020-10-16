<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateAngsuranCT;
use App\Http\Requests\ValidatePelunasanCT;

use App\Models\penjualan;
use App\Models\project_item;
use App\Models\penjualan_detail;
use App\Models\list_angsuran;
use App\Models\biaya;
use Carbon\Carbon;
use DataTables;
use WordTemplate;
use DB;
use Dompdf\Dompdf;
use Auth;

class AngsuranCashTempoController extends Controller
{
    public function __construct(){
       loadHelper('format,url');
    }

    function index($uuid){
    	$p = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();
    	if($p->tipe != 'Cash Tempo'){
            return back();
        }
        return view('transaksi.angsuran.cash-tempo', compact('p'));
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
	            		$bayar = '<a type="button" class="btn btn-warning text-white btn-xs" data-toggle="modal" data-target="#modal-bayar" data-uuid="'.$ang->uuid.'">Bayar</a>';
	            		$print = "";	            		
	            		if($ang->status == 1){
	            			$bayar = '';
		                	$print = '<a href="'.url_admin('angsuran/cash-tempo').'/'.$data->uuid.'/'.$ang->uuid.'" title="print kwitansi" target="__blank"><i class="fa fa-print natural"></i></a>';
	            		}
		                
						$isi .= "<tr>
								<td class='text-center'>".$i."</td>
								<td>".$ang->ket."</td>
								<td>".toDateDisplay($ang->rencana_tgl_bayar)."</td>
								<td>".toDateDisplay($ang->tgl_bayar)."</td>
								<td>".$ang->cara_bayar."</td>
                                <td class='text-right'>".toMoney($ang->rencana_bayar)."</td>
                                <td class='text-right'>".toMoney($ang->potongan)."</td>
                                <td class='text-right'>".toMoney($ang->jml_bayar)."</td>
                                <td class='text-right'>".toMoney($netto)."</td>
								<td class='text-center'>".$bayar." ".$print."</td>
							</tr>";
						
					}
				}

                $kredit='<table id="table" class="table dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
							<tbody>
					  			<tr style="background: #e9ecef;">
									<td style="width:10px;">No</td>
									<td style="width:150px;">Keterangan</td>
									<td style="width: 100px;">Tanggal JT</td>
									<td style="width: 100px;">Tanggal Bayar</td>
									<td style="width: 100px;">Cara Bayar</td>
									<td class="text-right" style="width: 100px;">Angsuran</td>
                                    <td class="text-right" style="width: 100px;">Potongan</td>
									<td class="text-right" style="width: 100px;">Nilai Bayar</td>
									<td class="text-right" style="width: 100px;">Sisa Piutang</td>
									<td class="text-center" style="width:5px;"">Aksi</td>
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

    function get_detail($uuid, $ud){
        $data = list_angsuran::with('penjualan')->whereUuid($ud)->firstOrFail();
        return response()->json($data);
    }

    function get_data($uuid){
        $data = penjualan::with('list_angsuran')->whereUuid($uuid)->firstOrFail();
        if($data->tipe != 'Cash Tempo'){
            return back();
        }

        return response()->json($data);
    }

    function submit_data(ValidateAngsuranCT $r, $uuid){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
        	$ud = trim($r->uuid);
        	$la = list_angsuran::with('penjualan')->whereUuid($ud)->firstOrFail();
        	if($la->penjualan->tipe != 'Cash Tempo' || $la->status == 1){
	            return back();
	        }

            //cek sebelum submit data
            if($la->penjualan->status == 2 && $la->penjualan->is_lunas == 1){
                return response()->json(['error' => 'Data tidak bisa disimpan!']);
            }

            //jika jumlah bayar lebih besar dari sisa piutang
            if(trim(toMoneyInput($r->jml_bayar)) > $la->rencana_bayar){
                return response()->json(['error' => 'Jumlah bayar tidak lebih besar dari angsuran!']);
            }else if(trim(toMoneyInput($r->jml_bayar)) <= 0){
                return response()->json(['error' => 'Jumlah bayar tidak valid!']);
            }

            //buat no kwitansi
            $cp = penjualan::whereStatus(2)->count();
            $cla = list_angsuran::whereStatus(1)->count();
            $nk = $cp+$cla+1;

            $la->jml_bayar 			= toMoneyInput(trim($r->jml_bayar));
            $la->cara_bayar 		= trim($r->cara_bayar);
            $la->tgl_bayar 			= trim(toDateSystem($r->tgl_bayar));
            $la->status 			= 1;
            $la->no_kwitansi 		= 'GKL-'.toZero(4,$nk);
            $la->save();

            //update data penjualan
            $pjl = penjualan::with(['penjualan_detail','list_angsuran'])->whereUuid($uuid)->firstOrFail();

            $ta = (int)$pjl->list_angsuran->sum('jml_bayar');
            $pot = (int)$pjl->list_angsuran->sum('potongan');
            $sp = $pjl->total_harga-($pjl->potongan+$pjl->uang_muka+$ta+$pot);
            $lunas = 0;
            if($sp == 0){
                $lunas = 1;
            }

            $pjl->total_angsuran    = $ta;
            $pjl->pot_angsuran      = $pot;
            $pjl->sisa_piutang      = $sp;
            $pjl->is_lunas          = $lunas;
            $pjl->save();

            //insert data cash flow
            $number_cf = biaya::count('id');
            $number_cf = $number_cf+1;
            $no_cf = 'TRANS-'.toZero(4,$number_cf);
            $no_kavling ='';
            $i=0;
            foreach ($pjl->penjualan_detail as $pd) {
                $no_kavling .= $i == 0 ? $pd->no_kavling : " | ".$pd->no_kavling;

                $i++;
            }
            $ket_cf = 'Cash Tempo Kav. '.$pjl->project.' No. '.$no_kavling.' a.n '.$pjl->nama;

            $b = new biaya();
            $b->no_transaksi    = $no_cf;
            $b->project_id      = $pjl->project_id;
            $b->tgl             = trim(toDateSystem($r->tgl_bayar));
            $b->code            = 'In';
            $b->keterangan      = $ket_cf;
            $b->jumlah          = toMoneyInput(trim($r->jml_bayar));
            $b->status          = 1;
            $b->save(); 

            DB::commit();
            return response()->json(['success' => 'Data barhasil disimpan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function pelunasan(ValidatePelunasanCT $r, $uuid){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
            $p = penjualan::with('list_angsuran')->whereUuid($uuid)->firstOrFail();
            if($p->tipe != 'Cash Tempo'){
	            return back();
	        }

            //cek sebelum submit data
            if($p->status == 2 && $p->is_lunas == 1){
                return response()->json(['error' => 'Data tidak bisa disimpan!']);
            }

            //jika jumlah bayar lebih besar dari sisa piutang
            $pot = (int)toMoneyInput(trim($r->potonganP));
            $nb = (int)toMoneyInput(trim($r->jml_bayarP));
            $sisa = $p->sisa_piutang;
            $jb = $sisa-$pot;
            if($jb < 0){
                return response()->json(['error' => 'Potongan tidak lebih besar dari sisa piutang!']);
            }

            if($jb < $nb || $nb == 0){
                return response()->json(['error' => 'Nilai bayar tidak sesuai!']);
            }

            //buat no kwitansi
            $cp = penjualan::whereStatus(2)->count();
            $cla = list_angsuran::whereStatus(1)->count();
            $nk = $cp+$cla+1;

            $ket = trim($r->ketP);
            $status = 0;
            if($sisa-($pot+$nb) == 0){
            	$status = 1;
            }

            $la = new list_angsuran();
            $la->penjualan_id       = $p->id;
            $la->ket                = $ket;
            $la->jml_bayar          = $nb;
            $la->potongan           = $pot;
            $la->cara_bayar         = trim($r->cara_bayarP);
            $la->tgl_bayar          = trim(toDateSystem($r->tgl_bayarP));
            $la->status             = 1;
            $la->no_kwitansi        = 'GKL-'.toZero(4,$nk);
            $la->save();

            //update data penjualan
            $pjl = penjualan::with(['penjualan_detail','list_angsuran'])->whereUuid($uuid)->firstOrFail();

            $ta = (int)$pjl->list_angsuran->sum('jml_bayar');
            $pot = (int)$pjl->list_angsuran->sum('potongan');
            $sp = $pjl->total_harga-($pjl->potongan+$pjl->uang_muka+$ta+$pot);

            $pjl->total_angsuran    = $ta;
            $pjl->pot_angsuran      = $pot;
            $pjl->sisa_piutang      = $sp;
            $pjl->is_lunas          = $status;
            $pjl->save();

            //insert data cash flow
            $number_cf = biaya::count('id');
            $number_cf = $number_cf+1;
            $no_cf = 'TRANS-'.toZero(4,$number_cf);
            $no_kavling ='';
            $i=0;
            foreach ($pjl->penjualan_detail as $pd) {
                $no_kavling .= $i == 0 ? $pd->no_kavling : " | ".$pd->no_kavling;

                $i++;
            }
            $ket_cf = 'Pelunasan Kav. '.$pjl->project.' No. '.$no_kavling.' a.n '.$pjl->nama;

            $b = new biaya();
            $b->no_transaksi    = $no_cf;
            $b->project_id      = $pjl->project_id;
            $b->tgl             = trim(toDateSystem($r->tgl_bayarP));
            $b->code            = 'In';
            $b->keterangan      = $ket_cf;
            $b->jumlah          = $nb;
            $b->status          = 1;
            $b->save();

            DB::commit();
            return response()->json(['success' => 'Data barhasil disimpan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function print($uuid, $ang_uuid){
        DB::beginTransaction();
        try{
        	$la = list_angsuran::with('penjualan.penjualan_detail')->whereUuid($ang_uuid)->firstOrFail();
        	if($la->penjualan->tipe != 'Cash Tempo'){
	            return back();
	        }
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
