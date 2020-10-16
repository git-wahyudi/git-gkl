<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateAngsuran;
use App\Http\Requests\ValidatePelunasan;

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

class AngsuranKreditController extends Controller
{
    public function __construct(){
       loadHelper('format,url');
    }

    function index($uuid){
    	$p = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();
        if($p->tipe != 'Kredit'){
            return back();
        }
        return view('transaksi.angsuran.kredit', compact('p'));
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
		                	$print = '<a href="'.url_admin('angsuran/kredit').'/'.$data->uuid.'/'.$ang->uuid.'" title="print kwitansi" target="__blank"><i class="fa fa-print natural"></i></a>';
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

    function get_detail($uuid){
        $data = penjualan::with('list_angsuran')->whereUuid($uuid)->firstOrFail();
        if($data->tipe != 'Kredit'){
            return back();
        }
        $jb = $data->angsuran;
        if($data->sisa_piutang < $data->angsuran){
        	$jb =  $data->sisa_piutang;
        }
        $angsuran_ke = $data->list_angsuran->count();
        $ang = "";
        if($angsuran_ke == 0){
        	for($i=1; $i<$data->tenor+1; $i++){
        		$ang .= '<option value="'.$i.'">Angsuran ke-'.$i.'</option>';
        	}
        }else {
        	for($i=1; $i<$data->tenor+1; $i++){
        		$accept = 0;
	        	foreach ($data->list_angsuran as $da) {
	        		if($da->angsuran_ke == $i && $da->status == 0){
	        			$ang .= '<option value="'.$i.'">Angsuran ke-'.$i.'</option>';
	        			$accept = 1;
	        		}else if($da->angsuran_ke == $i && $da->status == 1){
	        			$accept = 1;
	        		}
	        	}

	        	if($accept == 0){
	        		$ang .= '<option value="'.$i.'">Angsuran ke-'.$i.'</option>';
	        	}

        	}
        }

        return response()->json(['pjl'=>$data, 'ang'=>$ang, 'jb'=>$jb]);
    }

    function submit_data(ValidateAngsuran $r, $uuid){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
        	$p = penjualan::with('list_angsuran')->whereUuid($uuid)->firstOrFail();
            if($p->tipe != 'Kredit'){
                return back();
            }

            //cek sebelum submit data
            if($p->status == 2 && $p->is_lunas == 1){
                return response()->json(['error' => 'Data tidak bisa disimpan!']);
            }

            //jika ada yang coba curang
            if(list_angsuran::where([['penjualan_id',$p->id],['angsuran_ke',trim($r->ket)],['status',1]])->first()){
                return response()->json(['error' => 'Opss.. Terjadi kesalahan!']);
            }

            //jika jumlah bayar lebih besar dari sisa piutang
            if(trim(toMoneyInput($r->jml_bayar)) > $p->sisa_piutang){
                return response()->json(['error' => 'Jumlah bayar tidak lebih besar dari sisa piutang!']);
            }else if(trim(toMoneyInput($r->jml_bayar)) <= 0){
                return response()->json(['error' => 'Jumlah bayar tidak valid!']);
            }

            $qa = $p->list_angsuran->count(); //qty angsuran
            $angke = trim($r->ket); // angsuran ke
            $qaplus = $qa+1; //list angsuran tambah satu

            $cek = list_angsuran::where([['penjualan_id',$p->id],['angsuran_ke',$angke],['status',0]])->first();
            if(!$cek && $angke > $qaplus){
            	for ($i=$qaplus; $i < $angke; $i++) {
	            	$lab = new list_angsuran(); // list angsuran belum bayar
		            $lab->penjualan_id  	= $p->id;
		            $lab->angsuran_ke 		= $i;
		            $lab->ket 				= 'Angsuran ke-'.$i;
		            $lab->save();
            	}
            }

            //buat no kwitansi
            $cp = penjualan::whereStatus(2)->count();
            $cla = list_angsuran::whereStatus(1)->count();
            $nk = $cp+$cla+1;


            $ket = 'Angsuran ke-'.trim($r->ket);

            $la = list_angsuran::where([['penjualan_id',$p->id],['angsuran_ke',trim($r->ket)],['status',0]])->first();
            if(!$la){
            	$la = new list_angsuran();
            }
            $la->penjualan_id  		= $p->id;
            $la->angsuran_ke 		= trim($r->ket);
            $la->ket 				= $ket;
            $la->jml_bayar 			= toMoneyInput(trim($r->jml_bayar));
            $la->cara_bayar 		= trim($r->cara_bayar);
            $la->tgl_bayar 			= trim(toDateSystem($r->tgl_bayar));
            $la->catatan 			= trim($r->catatan);
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
            $ket_cf = $ket.' Kav. '.$pjl->project.' No. '.$no_kavling.' a.n '.$pjl->nama;

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

    function pelunasan(ValidatePelunasan $r, $uuid){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
            $p = penjualan::with('list_angsuran')->whereUuid($uuid)->firstOrFail();
            if($p->tipe != 'Kredit'){
                return back();
            }

            //cek sebelum submit data
            if($p->status == 2 && $p->is_lunas == 1){
                return response()->json(['error' => 'Data tidak bisa disimpan!']);
            }

            //jika jumlah bayar lebih besar dari sisa piutang
            $pot = (int)toMoneyInput(trim($r->potonganP));
            $sisa = $p->sisa_piutang;
            $jb = $sisa-$pot;
            if($jb < 0){
                return response()->json(['error' => 'Potongan tidak lebih besar dari sisa piutang!']);
            }

            //buat no kwitansi
            $cp = penjualan::whereStatus(2)->count();
            $cla = list_angsuran::whereStatus(1)->count();
            $nk = $cp+$cla+1;

            $ket = 'Pelunasan';

            $la = new list_angsuran();
            $la->penjualan_id       = $p->id;
            $la->ket                = $ket;
            $la->jml_bayar          = $jb;
            $la->potongan           = $pot;
            $la->cara_bayar         = trim($r->cara_bayarP);
            $la->tgl_bayar          = trim(toDateSystem($r->tgl_bayarP));
            $la->catatan            = trim($r->catatanP);
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
            $pjl->is_lunas          = 1;
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
            $b->jumlah          = $jb;
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
            if($la->penjualan->tipe != 'Kredit'){
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
