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
						<td class="text-right" colspan="6">Total Harga Kavling</td>
						<td class="text-right">{{toMoney($p->total_harga-$p->potongan)}}</td>
					</tr>
					<tr>
						<td class="text-right" colspan="6">Uang Muka</td>
						<td class="text-right">{{toMoney($p->uang_muka)}}</td>
					</tr>
					<tr>
						<td class="text-right" colspan="6">Piutang</td>
						<td class="text-right">{{toMoney($p->total_harga-$p->potongan-$p->uang_muka)}}</td>
					</tr>
				</tfoot>
			</table>			

	        <div>
	            @if(ucc() && $p->status == 2 && $p->is_lunas == 0)
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
{{Html::bsModalOpenLg('angsuran/cash-tempo/'.$p->uuid.'/add', 'bayar', 'Detail Pembayaran')}}
	{{Form::bsHidden('uuid')}}
	{{Form::bsSelect('cara_bayar', 'Cara Bayar', ['Tunai'=>'Tunai', 'Transfer'=>'Transfer'], 'true','')}}
	{{Form::bsText('tgl_bayar', 'Tanggal', date('d-m-Y'), 'true','tanggal datepicker')}}
	{{Form::bsText('jml_bayar', 'Nilai Bayar', '', 'true','rupiah')}}
{{Html::bsModalClose('Simpan')}}

{{Html::bsModalOpenLg('angsuran/cash-tempo/'.$p->uuid.'/pelunasan', 'pelunasan', 'Detail Pelunasan')}}
	{{Form::bsRoText('spP', 'Sisa Piutang', '', '','rupiah')}}
	{{Form::bsSelect('cara_bayarP', 'Cara Bayar', ['Tunai'=>'Tunai', 'Transfer'=>'Transfer'], 'true','')}}
	{{Form::bsText('tgl_bayarP', 'Tanggal', date('d-m-Y'), 'true','tanggal datepicker')}}
	{{Form::bsText('potonganP', 'Potongan', '', '','rupiah hitung')}}
	{{Form::bsText('jml_bayarP', 'Nilai Bayar', '', 'true','rupiah')}}
	{{Form::bsTextArea('ketP','Keterangan','','true','')}}
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
			ajax: "{{url_admin('angsuran/cash-tempo')}}/{{$p->uuid}}/datatable",
			columns: [
				{ data: 'data'}
			]
		});

		{{Html::jsShowModal('bayar')}}
			var ud = $(e.relatedTarget).data('uuid');
			$.ajax({
                url:"{{url_admin('angsuran/cash-tempo')}}/{{$p->uuid}}/get-detail/"+ud,
                success:function(data){
					{{Html::jsValueForm('uuid','input','ud')}}
					{{Html::jsValueForm('jml_bayar','money','data.rencana_bayar')}}
				},
                error:function(data){
                    $("#modal-bayar").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
		{{Html::jsCloseModal()}}
		
		$('#bayar').ajaxForm({
		    beforeSubmit:function(){
		        $(".submit").attr("disabled", true);
		    },
		    success:function(data){
		        $("#modal-bayar").modal('hide');
		        $('#table_kredit').DataTable().ajax.url("{{url_admin('angsuran/cash-tempo')}}/{{$p->uuid}}/datatable").load();
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
                url:"{{url_admin('angsuran/cash-tempo')}}/{{$p->uuid}}/get-data",
                success:function(data){
					{{Html::jsValueForm('jml_bayarP','money','data.sisa_piutang')}}
					{{Html::jsValueForm('spP','money','data.sisa_piutang')}}
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
		        $('#table_kredit').DataTable().ajax.url("{{url_admin('angsuran/cash-tempo')}}/{{$p->uuid}}/datatable").load();
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