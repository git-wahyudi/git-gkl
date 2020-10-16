<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidatePenjualanKreditDetail;
use App\Http\Requests\ValidateDetailKredit;

use App\Models\penjualan;
use App\Models\project_item;
use App\Models\penjualan_detail;
use App\Models\biaya;
use Carbon\Carbon;
use Dompdf\Dompdf;
use DataTables;
use WordTemplate;
use Auth;
use DB;

class PenjualanKreditController extends Controller
{
    public function __construct(){
       loadHelper('format');
    }

    function index($uuid){
    	$p = penjualan::whereUuid($uuid)->firstOrFail();
    	$pi = project_item::where('project_id',$p->project_id)->select('id as value','no_kavling as text')->get();

    	if($p->tipe != 'Kredit'){
    		return back();
    	}
    	
        return view('transaksi.penjualan.kredit', compact('p','pi'));
    	
    }

    function datatable($uuid){
        ini_set('memory_limit', '-1');
        $data = penjualan::with('penjualan_detail')->whereUuid($uuid);
        return Datatables::of($data)
            ->addColumn('data', function($data){
            	$isi ="<tr><td colspan='6' class='text-center'>Tidak ada data.</td></tr>";
            	if($data->penjualan_detail->count() > 0){
            		$i = 0;
            		$isi = "";
	            	foreach ($data->penjualan_detail as $pd) {
	            		$i = $i+1;
	            		$delete="";
		                if($this->ucd()){
		                    $delete = '<a href="#" data-toggle="modal" data-target="#modal-delete-data" title="hapus" data-uuid="'.$pd->uuid.'"><i class="fa fa-trash-o natural"></i></a>';
		                }

		                $action = $delete;
		                if ($action=='' || $data->status > 1){$action='<a href="#" class="act"><i class="ti ti-lock natural"></i></a>'; }

						$isi .= "<tr>
								<td class='text-center'>".$i."</td>
								<td>".$pd->no_kavling."</td>
								<td>".$pd->luas."</td>
								<td class='text-right'>".toMoney($pd->harga)."</td>
								<td class='text-right'>".toMoney($pd->total_harga)."</td>
								<td class='text-center'>".$action."</td>
							</tr>";
						
					}
				}

                $kredit='<table id="table" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
				        <tbody>	
							<tr style="background: #e9ecef;">
								<td style="width:10px;">No</td>
								<td>No Kavling</td>
								<td style="width: 100px;">Luas /M2</td>
								<td class="text-right" style="width: 120px;">Harga /M2</td>
								<td class="text-right" style="width: 120px;">Total Harga</td>
								<td style="width: 50px;text-align: center;">Aksi</td>
							</tr>
                            '.$isi.'
                        </tbody>
					</table>

					<table id="table" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
				        <tbody> 
                            <tr style="background: #e9ecef;">
								<td style="width: 180px;">Total Harga Kavling</td>
                                <td style="width: 150px;">Uang Muka</td>
								<td style="width: 150px;">Sisa Piutang</td>
								<td style="width: 80px;">Tenor</td>
								<td style="width: 180px;">Angsuran Perbulan</td>
								<td class="text-center" style="width: 120px;">Tgl Jatuh Tempo</td>
								<td class="text-center" style="width: 120px;">Tgl Selesai</td>
							</tr>
							<tr>
								<td>Rp '.toMoney($data->penjualan_detail->sum('total_harga')).'</td>
                                <td>Rp '.toMoney($data->uang_muka).' ('.$data->cara_bayar.')'.'</td>
								<td>Rp '.toMoney($data->sisa_piutang).'</td>
								<td>'.toMoney($data->tenor).' bulan</td>
								<td>Rp '.toMoney($data->angsuran).'</td>
								<td class="text-center">'.toDateDisplay($data->tgl_bayar).'</td>
								<td class="text-center">'.toDateDisplay($data->tgl_selesai).'</td>
							</tr>
						</tbody>
					</table>';
                return $kredit;
            })
            ->addIndexColumn()
            ->rawColumns(['data'])
            ->make(true);
    }

    function get_record($uuid, $detail){
        $data = penjualan_detail::whereUuid($detail)->firstOrFail();
        return response()->json($data);
    }

    function get_detail($uuid){
        $data = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();
        $total = $data->penjualan_detail->sum('total_harga');
        return response()->json(['pjl'=>$data,'total'=>$total]);
    }

    function submit_data(ValidatePenjualanKreditDetail $r, $uuid){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{
        	$p = penjualan::whereUuid($uuid)->firstOrFail();
            if($p->tipe != 'Kredit'){
                return back();
            }

        	$pi = project_item::where([['id',trim($r->no)],['project_id',$p->project_id]])->firstOrFail();
        	if($pi->status > 0){
        		return response()->json(['error' => 'No kavling sudah terjual!']);
        	}

            //cek sebelum submit data
            if($p->status == 2){
                return response()->json(['error' => 'Data tidak bisa disimpan!']);
            }

        	$pi->status = 1; //sudah terjual
        	$pi->save();

            $pd = new penjualan_detail();
            $pd->penjualan_id  		= $p->id;
            $pd->project_id     	= $p->project_id;
            $pd->project_item_id    = $pi->id;
            $pd->no_kavling     	= $pi->no_kavling;
            $pd->luas 				= $pi->luas;
            $pd->harga 				= $pi->harga;
            $pd->total_harga 		= $pi->total_harga;
            $pd->save();

            //update data penjualan
            $pjl = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();

            $total = (int)$pjl->penjualan_detail->sum('total_harga');
            $dp = (int)$pjl->uang_muka;
            $piutang = $total-$dp;
            $tenor = (int)$pjl->tenor;
            $cicilan = $tenor > 0 ? $piutang/$tenor : 0;
            $angsuran = $tenor > 0 ? (int) (1000 * ceil($cicilan / 1000)) : 0;

            $pjl->total_harga       = $total;
            $pjl->sisa_piutang      = $piutang;
            $pjl->angsuran          = $angsuran;
            $pjl->save();

            DB::commit();
            return response()->json(['success' => 'Data barhasil disimpan!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }

    function update_data(ValidateDetailKredit $r, $uuid){
        if(!$this->ucu()){
            return response()->json(['error' => 'Anda tidak memiliki hak akses!']);
        }

        DB::beginTransaction();
        try{

        	$pjl = penjualan::with('penjualan_detail')->where('uuid',$uuid)->firstOrFail();
            if($pjl->tipe != 'Kredit'){
                return back();
            }

            //cek sebelum update data
            if($pjl->status == 2){
                return response()->json(['error' => 'Data tidak bisa diubah!']);
            }

            $total = (int)$pjl->penjualan_detail->sum('total_harga');
            $dp = (int)trim(toMoneyInput($r->uang_muka));
            $piutang = $total-$dp;

            if($piutang <= 0){
                return response()->json(['error' => 'Nilai uang muka tidak lebih besar dari piutang!']);
            }

            $tenor = (int)trim(toMoneyInput($r->tenor))-1;
            $tgl_bayar = trim(toDateSystem($r->tgl_bayar));
            $selesai = date('Y-m-d', strtotime("+".$tenor." months", strtotime($tgl_bayar)));

            $pjl->uang_muka       	= $dp;
            $pjl->cara_bayar        = $r->cara_bayar;
            $pjl->sisa_piutang      = $piutang;
            $pjl->tenor             = trim(toMoneyInput($r->tenor));
            $pjl->angsuran          = trim(toMoneyInput($r->angsuran));
            $pjl->tgl_bayar         = $tgl_bayar;
            $pjl->tgl_selesai       = $selesai;
            $pjl->save();

            DB::commit();
            return response()->json(['success' => 'Data barhasil diubah!']);
        }catch (\Exception $e){
            DB::rollback();
            return response()->json(['error' => 'Periksa kembali data!']);
        }  
    }


    function delete_data(Request $r, $uuid){
        if(!$this->ucd()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        DB::beginTransaction();
        try{
        	
            //update data penjualan
            $pjl = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();
            if($pjl->tipe != 'Kredit'){
                return back();
            }

            if($pjl->status == 2){
                return response()->json(['error' => 'Data tidak bisa dihapus!']);
            }

            //Delete penjualan
            $uuidD = trim($r->uuidD);
            $pd = penjualan_detail::whereUuid($uuidD)->firstOrFail();
            $pd->delete();

            //update status kavling jadi available
            $pi = project_item::whereId($pd->project_item_id)->firstOrFail();
            $pi->status = 0;
            $pi->save();


            $p = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();
            $total = (int)$p->penjualan_detail->sum('total_harga');
            $dp = (int)$p->uang_muka;
            $piutang = $total > 0 ? $total-$dp : 0;
            $tenor = (int)$p->tenor;
            $cicilan = $tenor > 0 ? $piutang/$tenor : 0;
            $angsuran = $tenor > 0 ? (int) (1000 * ceil($cicilan / 1000)) : 0;

            $pjl->total_harga       = $total;
            $pjl->sisa_piutang      = $piutang;
            $pjl->angsuran          = $angsuran;
            $pjl->save();

            
            DB::commit();
            return response()->json(['success' => 'Data berhasil dihapus!']);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['error' => 'Periksa kembali data!']);
        } 
    }

    function posting_data($uuid){
        if(!$this->ucd()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        DB::beginTransaction();
        try{
            //create no transaksi
            $number = penjualan::where([['tgl_penjualan',date('Y-m-d')],['status',2]])->count();
            $number = $number+1;
            $no = 'GKL'.date('Ymd').'-'.toZero(3,$number);

            //buat no kwitansi
            $cp = penjualan::whereStatus(2)->count();
            $cla = list_angsuran::whereStatus(1)->count();
            $nk = $cp+$cla+1;

            //update data penjualan
            $pjl = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();
            if($pjl->tipe != 'Kredit'){
                return back();
            }
            if($pjl->status == 2){
                return response()->json(['error' => 'Data tidak bisa diubah!']);
            }

            if($pjl->penjualan_detail->count() == 0){
                return response()->json(['error' => 'Data kavling belum di input!']);
            }

            if($pjl->sisa_piutang <= 0){
                return response()->json(['error' => 'Nilai uang muka tidak lebih besar/sama dari piutang!']);
            }

            $pjl->no_transaksi      = $no;
            $pjl->no_kw_um          = 'GKL-'.toZero(4,$nk);
            $pjl->status            = 2;
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
            $ket_cf = 'DP Kav. '.$pjl->project.' No. '.$no_kavling.' a.n '.$pjl->nama;

            $b = new biaya();
            $b->no_transaksi    = $no_cf;
            $b->project_id      = $pjl->project_id;
            $b->tgl             = $pjl->tgl_penjualan;
            $b->code            = 'In';
            $b->keterangan      = $ket_cf;
            $b->jumlah          = $pjl->uang_muka;
            $b->status          = 1;
            $b->save();
            
            DB::commit();
            return response()->json(['success' => 'Data berhasil disimpan!']);
        }catch (\Exception $e){
            DB::rollBack();
            return response()->json(['error' => 'Periksa kembali data!']);
        } 
    }

    function spjb($uuid){
        if(!$this->ucc()){
            return response()->json(['error' => 'Anda tidak memiliki akses!']);
        }

        
            $pjl = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();
            if($pjl->tipe != 'Kredit'){
                return back();
            }
            $file = public_path('template/spjb-kredit.rtf');

            if($pjl->penjualan_detail->count() == 1){
                foreach ($pjl->penjualan_detail as $pd) {
                    $no_kavling = $pd->no_kavling;
                    $luas       = toMoney($pd->luas)." m2";
                    $harga      = "Rp. ".toMoney($pd->harga);
                }
            }else {
                $i = 0;
                $no_kavling ='';
                $luas ='';
                $harga ='';
                foreach ($pjl->penjualan_detail as $pd) {
                    $no_kavling .= $i == 0 ? $pd->no_kavling : " | ".$pd->no_kavling;
                    $luas       .= $i == 0 ? toMoney($pd->luas).' m2' : " | ".toMoney($pd->luas)." m2";
                    $harga      .= $i == 0 ? "Rp. ".toMoney($pd->harga) : " | Rp. ".toMoney($pd->harga);

                    $i++;
                }
            }
        
            $array = array(
                '[o_nm]'    => strtoupper($pjl->o_nama),
                '[o_nik]'   => $pjl->o_nik,
                '[o_ttl]'   => $pjl->o_ttl,
                '[o_jk]'    => $pjl->o_jk,
                '[o_ad]'    => $pjl->o_alamat,
                '[o_ag]'    => $pjl->o_agama,
                '[o_pk]'    => $pjl->o_pekerjaan,
                '[o_hp]'    => $pjl->o_telp,
                '[p_nm]'    => strtoupper($pjl->nama),
                '[p_nik]'   => $pjl->nik,
                '[p_ttl]'   => $pjl->ttl,
                '[p_jk]'    => $pjl->jk,
                '[p_ad]'    => $pjl->alamat,
                '[p_ag]'    => $pjl->agama,
                '[p_pk]'    => $pjl->pekerjaan,
                '[p_hp]'    => $pjl->telp,
                '[pa]'      => $pjl->project_alamat,
                '[nk]'      => $no_kavling,
                '[lk]'      => $luas,
                '[hp]'      => $harga,
                '[th]'      => toMoney($pjl->total_harga),
                '[um]'      => toMoney($pjl->uang_muka),
                '[piu]'     => toMoney($pjl->sisa_piutang),
                '[ang]'     => toMoney($pjl->angsuran),
                '[tn]'      => toMoney($pjl->tenor),
                '[jt]'      => toDateDisplay($pjl->tgl_bayar),
                '[st]'      => toDateDisplay($pjl->tgl_selesai),
                '[tgl]'     => tgl_indo($pjl->tgl_penjualan),
                '[penjual]' => strtoupper($pjl->o_nama),
                '[buyer]'   => strtoupper($pjl->nama),
                '[saksi_p]' => strtoupper($pjl->saksi_pertama),
                '[saksi_b]' => strtoupper($pjl->saksi_kedua),
                '[project]' => $pjl->project
                
            );

            $nama_file = 'SPJB-'.strtoupper(str_replace(' ', '-',$pjl->nama)).'.docx';
            
            return WordTemplate::export($file, $array, $nama_file);
    }

    function kwitansi($uuid){
        DB::beginTransaction();
        try{
            $pjl = penjualan::with('penjualan_detail')->whereUuid($uuid)->firstOrFail();
            if($pjl->tipe != 'Kredit'){
                return back();
            }
            $i = 0;
            $kavling ='';
            foreach ($pjl->penjualan_detail as $pd) {
                $kavling .= $i == 0 ? $pd->no_kavling." (".toMoney($pd->luas).' m2)' : " | ".$pd->no_kavling." (".toMoney($pd->luas).' m2)';

                $i++;
            }

            $date = Date('d-m-Y h:i:s');
            $ket = 'DP';

            $html = view('transaksi/penjualan/kwitansi',compact('pjl','kavling','ket'));
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            // $dompdf->setPaper(array(0,0,609.4488,935.433), 'Potrait');
            $dompdf->setPaper(array(0,0,595,276,420,94488), 'Potrait');
            // $dompdf->setPaper('A4', 'Potrait');
            $dompdf->render();
            $canvas = $dompdf ->get_canvas();
            // $canvas->page_text(525, 255, "Page {PAGE_NUM} of {PAGE_COUNT}", null, 10, array(0, 0, 0));
            $canvas->page_text(23, 255, "Print at : ".$date." by ".ucwords(Auth::user()->nama), null, 8, array(0, 0, 0));
            $dompdf->stream('print-kwitansi-dp.pdf', array("Attachment" => false));

            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            echo "<script>window.close();</script>";
        }
    }
}
