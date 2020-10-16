@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2-bootstrap.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/bootstrap-datepicker.css')}}" rel="stylesheet" />
<link href="{{asset('css/jquery-ui.css')}}" rel="stylesheet" />
<style type="text/css">
	table.dataTable {
		border-collapse: collapse;
	}

	table.dataTable th {
	 -webkit-box-sizing:content-box;
	 box-sizing:content-box;
	 border-top:0px !important;
	}
</style>
@endsection
@section('content')
<?php 
	date_default_timezone_set('Asia/Jakarta');
    loadHelper('format,url,akses');
?>
<div class="ibox animated fadeInDown">
    <div class="ibox-head">
        <div class="ibox-title"><i class="fa fa-clone mr-2"></i>Detail Transaksi Angsuran {{$p->no_transaksi}}</div>
    </div>
    <div class="ibox-body">
        <div class="table-responsive">
        	<div class="row">
			    <div class="col-md-6">
			        {{Form::bsRoText('konsumen','Nama',$p->nama,'','')}}
			        {{Form::bsRoText('project', 'Project', $p->project, '', '')}}
			    </div>
			    <div class="col-md-6">
			        {{Form::bsRoText('tgl_pjl','Tanggal Transaksi',toDateDisplay($p->tgl_penjualan),'true','tanggal')}}
			        {{Form::bsRoText('tp','Tipe Penjualan',$p->tipe,'','')}}
			    </div>
			</div>
			<table class="table dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
				<tbody>
					<tr style="background: #e9ecef;border-color: red !important;">
						<td class="text-center" style="width:30px;">No</td>
						<td>No Kavling</td>
						<td class="text-right" style="width: 100px;">Luas /M2</td>
						<td class="text-right" style="width: 120px;">Harga /M2</td>
                        <td class="text-right" style="width: 120px;">Total</td>
                        <td class="text-right" style="width: 120px;">Potongan</td>
						<td class="text-right" style="width: 120px;">Grand Total</td>
					</tr>
					@foreach($p->penjualan_detail as $pd)
					<tr>
						<td class="text-center">{{$pd->incrementing}}</td>
						<td>{{$pd->no_kavling}}</td>
						<td class="text-right">{{toMoney($pd->luas)}}</td>
						<td class="text-right">{{toMoney($pd->harga)}}</td>
						<td class="text-right">{{toMoney($pd->total_harga)}}</td>
						<td class="text-right">{{toMoney($pd->disc_value)}}</td>
						<td class="text-right">{{toMoney($pd->total_harga-$pd->disc_value)}}</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr style="background: #e9ecef;">
						<td colspan="7">Detail Kredit</td>
					</tr>
					<tr>
						<td class="text-right" colspan="3">Total Harga Kavling</td>
						<td class="text-right">{{toMoney($p->total_harga-$p->potongan)}}</td>
						<td class="text-right" colspan="2">Angsuran</td>
						<td class="text-right">{{toMoney($p->angsuran)}}</td>
					</tr>
					<tr>
						<td class="text-right" colspan="3">Uang Muka</td>
						<td class="text-right">{{toMoney($p->uang_muka)}}</td>
						<td class="text-right" colspan="2">Tenor</td>
						<td class="text-right">{{$p->tenor}} Bulan</td>
					</tr>
					<tr>
						<td class="text-right" colspan="3">Piutang</td>
						<td class="text-right">{{toMoney($p->total_harga-$p->potongan-$p->uang_muka)}}</td>
						<td class="text-right" colspan="2">Tanggal JT</td>
						<td class="text-right">{{toDateDisplay($p->tgl_bayar)}}</td>
					</tr>
				</tfoot>
			</table>			

	        <div>
	            @if(ucc() && $p->status == 2 && $p->is_lunas == 0)
	            <a type="button" class="btn btn-success text-white" data-toggle="modal" data-target="#modal-tambah-data"><i class="fa fa-plus-square-o mr-2"></i>Angsuran</a>
	            <a type="button" class="btn btn-warning text-white" data-toggle="modal" data-target="#modal-pelunasan"><i class="fa fa-plus-square-o mr-2"></i>Pelunasan</a>
	            @endif
	            <a href="{{url_admin('angsuran')}}" class="btn btn-danger text-white"><i class="fa fa-arrow-circle-o-left mr-2"></i>Kembali</a>
	        </div>

	        <table id="table_kredit"></table>
		</div>
    </div>
</div>
@endsection()

@section('modal')
@if(ucc() && $p->status == 2 && $p->is_lunas == 0)
{{Html::bsModalOpenLg('angsuran/kredit/'.$p->uuid.'/add', 'tambah-data', 'Detail Pembayaran')}}
	{{Form::bsSelect2('ket', 'Keterangan', '', 'true','clear')}}
	{{Form::bsSelect('cara_bayar', 'Cara Bayar', ['Tunai'=>'Tunai', 'Transfer'=>'Transfer'], 'true','')}}
	{{Form::bsText('tgl_bayar', 'Tanggal', date('d-m-Y'), 'true','tanggal datepicker')}}
	{{Form::bsText('jml_bayar', 'Nilai Bayar', '', 'true','rupiah')}}
	{{Form::bsTextArea('catatan','Catatan','','','')}}
{{Html::bsModalClose('Simpan')}}

{{Html::bsModalOpenLg('angsuran/kredit/'.$p->uuid.'/pelunasan', 'pelunasan', 'Detail Pelunasan')}}
	{{Form::bsRoText('spP', 'Sisa Piutang', '', '','rupiah')}}
	{{Form::bsSelect('cara_bayarP', 'Cara Bayar', ['Tunai'=>'Tunai', 'Transfer'=>'Transfer'], 'true','')}}
	{{Form::bsText('tgl_bayarP', 'Tanggal', date('d-m-Y'), 'true','tanggal datepicker')}}
	{{Form::bsText('potonganP', 'Potongan', '', '','rupiah hitung')}}
	{{Form::bsRoText('jml_bayarP', 'Nilai Bayar', '', 'true','rupiah')}}
	{{Form::bsTextArea('catatanP','Catatan','','','')}}
{{Html::bsModalClose('Simpan')}}
@endif()
@endsection()

@section('js')
<script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('js/jquery.form.min.js')}}"></script>
<script src="{{asset('js/select2.min.js')}}"></script>
<script src="{{asset('js/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js/jquery-ui.js')}}"></script>
<script src="{{asset('js/jquery.mask.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		function formatNumber(num) {
	        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
	    }

        $("#table_kredit").DataTable({
			processing: true,
			serverSide: true,
			bLengthChange: false,
			bFilter: false,
			bInfo: false,
			bPaginate: false,
			order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
			ajax: "{{url_admin('angsuran/kredit')}}/{{$p->uuid}}/datatable",
			columns: [
				{ data: 'data'}
			]
		});

		{{Html::jsShowModal('tambah-data')}}
			$.ajax({
                url:"{{url_admin('angsuran/kredit')}}/{{$p->uuid}}/get-detail",
                success:function(data){
                	var sp = data.pjl.sisa_piutang-data.jb;
                    $('#ket').append(data.ang);
					{{Html::jsValueForm('jml_bayar','money','data.jb')}}
				},
                error:function(data){
                    $("#modal-tambah-data").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
		{{Html::jsCloseModal()}}
		
		$('#tambah-data').ajaxForm({
		    beforeSubmit:function(){
		        $(".submit").attr("disabled", true);
		    },
		    success:function(data){
		        $("#modal-tambah-data").modal('hide');
		        $('#table_kredit').DataTable().ajax.url("{{url_admin('angsuran/kredit')}}/{{$p->uuid}}/datatable").load();
		        if (data.success){
		            {{Html::jsAlertTrue()}}
		        }else{
		            {{Html::jsAlertFalse()}}
		        }
		    },
		    error:function(data){
		        $(".submit").removeAttr("disabled");
		        {{Html::jsValidate()}}
		    }
		}); 

		{{Html::jsShowModal('pelunasan')}}
			$.ajax({
                url:"{{url_admin('angsuran/kredit')}}/{{$p->uuid}}/get-detail",
                success:function(data){
                	var sp = data.pjl.sisa_piutang;
					{{Html::jsValueForm('jml_bayarP','money','sp')}}
					{{Html::jsValueForm('spP','money','sp')}}
				},
                error:function(data){
                    $("#modal-pelunasan").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
		{{Html::jsCloseModal()}}
		
		$('#pelunasan').ajaxForm({
		    beforeSubmit:function(){
		        $(".submit").attr("disabled", true);
		    },
		    success:function(data){
		        $("#modal-pelunasan").modal('hide');
		        $('#table_kredit').DataTable().ajax.url("{{url_admin('angsuran/kredit')}}/{{$p->uuid}}/datatable").load();
		        if (data.success){
		            {{Html::jsAlertTrue()}}
		        }else{
		            {{Html::jsAlertFalse()}}
		        }
		    },
		    error:function(data){
		        $(".submit").removeAttr("disabled");
		        {{Html::jsValidate()}}
		    }
		});

		$('#posting').ajaxForm({
		    beforeSubmit:function(){
		        $(".submit").attr("disabled", true);
		    },
		    success:function(data){
		        $("#modal-posting").modal('hide');
		        if (data.success){
		        	window.location.reload()
		        }else{
		            {{Html::jsAlertFalse()}}
		        }
		    },
		    error:function(data){
		        $(".submit").removeAttr("disabled");
		        {{Html::jsValidate()}}
		    }
		});

		$("#pelunasan .hitung").keyup(function(){
			var sisa = parseInt($("#pelunasan #spP").val().replace(/\./g,''));
			var pot = parseInt($("#pelunasan #potonganP").val().replace(/\./g,''));
			if(isNaN(sisa) || sisa.length==0 || sisa == ""){
                sisa =0;           
            }
            if(isNaN(pot) || pot.length==0 || pot == ""){
                pot =0;           
            }
  
			var sp = sisa-pot;
			if(sp<0){
				sp = 0;
			}

			$("#pelunasan #jml_bayarP").val(formatNumber(sp));
		});

		$(".modal").on('hidden.bs.modal', function(){
            $(".form").trigger("reset");
            $(".error").addClass("d-none");
            $("select.select2").select2({ allowClear: true });
            $(".clear option[value!='']").remove();
            $(".submit").removeAttr("disabled");
        });
	});
</script>
@endsection()