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
        <div class="ibox-title"><i class="fa fa-clone mr-2"></i>Detail Penjualan Cash</div>
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
	        @if(ucc() && $p->status == 1)
	        <div>
	            <a type="button" class="btn btn-success text-white" data-toggle="modal" data-target="#modal-tambah-data" style="z-index: 10;"><i class="fa fa-plus-square-o mr-2"></i>Tambah Kavling</a>
	            <a type="button" class="btn btn-primary text-white" data-toggle="modal" data-target="#modal-detail-kredit" style="z-index: 10;"><i class="fa fa-plus-square-o mr-2"></i>Input Pembayaran</a>
	        </div>
	        @endif
	        <table id="table_kredit" style="border-collapse: collapse; border-spacing: 0;"></table>

			<div class="col-md-12 text-right pr-0">
		        <a href="{{url_admin('penjualan')}}" class="btn btn-danger text-white"><i class="fa fa-arrow-circle-o-left mr-2"></i>Kembali</a>
	        	@if(ucc() && $p->status == 1)
		        <a href="#" class="btn btn-default text-white" data-toggle="modal" data-target="#modal-posting"><i class="fa fa-check-square-o mr-1"></i>Simpan</a>
		    	@else
		    	<a href="{{url_admin('penjualan/cash')}}/{{$p->uuid}}/spjb" class="btn btn-default text-white"><i class="fa fa-download mr-1"></i>SPJB</a>
		    	<a href="{{url_admin('penjualan/cash')}}/{{$p->uuid}}/kwitansi" class="btn btn-default text-white" target="__blank"><i class="fa fa-file-text-o mr-1"></i>Kwitansi Pembayaran</a>
		    	@endif
		    </div>
		</div>
    </div>
</div>
@endsection()

@section('modal')
@if(ucc() && $p->status == 1)
{{Html::bsModalOpenLg('penjualan/cash/'.$p->uuid.'/add', 'tambah-data', 'Tambah Data Kavling')}}
	{{Form::bsRoText('project', 'Project', $p->project, '')}}
	{{Form::bsSelect2('no', 'No Kavling',$pi,'true','')}}
	{{Form::bsText('disc', 'Potongan (%)', 10, '','angka')}}
{{Html::bsModalClose('Simpan')}}

{{Html::bsModalOpenLg('penjualan/cash/'.$p->uuid.'/update', 'detail-kredit', 'Detail Pembayaran')}}
	{{Form::bsRoText('total_harga', 'Total Harga', '', '','rupiah')}}
	{{Form::bsText('nilai_bayar', 'Nilai Bayar', '', 'true','rupiah')}}
	{{Form::bsSelect('cara_bayar', 'Cara Bayar (Uang Muka)', ['Tunai'=>'Tunai', 'Transfer'=>'Transfer'], 'true','')}}
	{{Form::bsText('tgl_bayar', 'Tanggal Bayar', date('d-m-Y'), 'true','tanggal datepicker')}}
{{Html::bsModalClose('Simpan')}}

{{Html::bsModalOpen('penjualan/cash/'.$p->uuid.'/posting', 'posting', 'Simpan Data')}}
	<div class="alert alert-danger text-white text-center"><strong>Data yang sudah disimpan tidak dapat diubah kembali, Pastikan data sudah lengkap!</strong></div>
{{Html::bsModalClose('Simpan')}}
@endif()

@if(ucd() && $p->status == 1)
{{Html::bsModalOpenLg('penjualan/cash/'.$p->uuid.'/delete', 'delete-data', 'Hapus Data')}}
	{{Form::bsHidden('uuidD')}}
	{{Form::bsRoText('projectD', 'Project', $p->project, '')}}
	{{Form::bsRoText('noD', 'No Kavling','','','')}}
{{Html::bsModalClose('Hapus')}}
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
			ajax: "{{url_admin('penjualan/cash')}}/{{$p->uuid}}/datatable",
			columns: [
				{ data: 'data'}
			]
		});

		{{Html::jsShowModal('tambah-data')}}
		{{Html::jsCloseModal()}}
		$('#tambah-data').ajaxForm({
		    beforeSubmit:function(){
		        $(".submit").attr("disabled", true);
		    },
		    success:function(data){
		        $("#modal-tambah-data").modal('hide');
		        $('#table_kredit').DataTable().ajax.url("{{url_admin('penjualan/cash')}}/{{$p->uuid}}/datatable").load();
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

		{{Html::jsShowModal('delete-data')}}
			var uuid = $(e.relatedTarget).data('uuid');
			$.ajax({
                url:"{{url_admin('penjualan/cash')}}/{{$p->uuid}}/get-record/"+uuid,
                success:function(data){
					{{Html::jsValueForm('uuidD','input','uuid')}}
					{{Html::jsValueForm('noD','input','data.no_kavling')}}
				},
                error:function(data){
                    $("#modal-delete-data").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
		{{Html::jsCloseModal()}}

		$('#delete-data').ajaxForm({
		    beforeSubmit:function(){
		        $(".submit").attr("disabled", true);
		    },
		    success:function(data){
		        $("#modal-delete-data").modal('hide');
		        $('#table_kredit').DataTable().ajax.url("{{url_admin('penjualan/cash')}}/{{$p->uuid}}/datatable").load();
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


		{{Html::jsShowModal('detail-kredit')}}
			$.ajax({
                url:"{{url_admin('penjualan/cash')}}/{{$p->uuid}}/get-detail",
                success:function(data){
					{{Html::jsValueForm('total_harga','money','data.pjl.sisa_piutang')}}
					{{Html::jsValueForm('nilai_bayar','money','data.pjl.uang_muka')}}
					{{Html::jsValueForm('cara_bayar','select','data.cb')}}
				},
                error:function(data){
                    $("#modal-detail-kredit").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
		{{Html::jsCloseModal()}}
		
		$('#detail-kredit').ajaxForm({
		    beforeSubmit:function(){
		        $(".submit").attr("disabled", true);
		    },
		    success:function(data){
		        $("#modal-detail-kredit").modal('hide');
		        $('#table_kredit').DataTable().ajax.url("{{url_admin('penjualan/cash')}}/{{$p->uuid}}/datatable").load();
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

		$("#detail-kredit .hitung").keyup(function(){
			var total_harga = parseInt($("#detail-kredit #total_harga").val().replace(/\./g,''));
			if(!total_harga){
				total_harga = 0;
			}
			var tenor = parseInt($("#detail-kredit #tenor").val().replace(/\./g,''));
			if(!tenor){
				tenor = 1;
			}
			var uang_muka = parseInt($("#detail-kredit #uang_muka").val().replace(/\./g,''));
			if(!uang_muka){
				uang_muka = 0;
			}

			var sisa_piutang = total_harga-uang_muka;

			//besar angsuran
			var cicilan = sisa_piutang/tenor;
			var hasil = Math.ceil(cicilan / 1000) * 1000;

			$("#detail-kredit #angsuran").val(formatNumber(hasil));
			$("#detail-kredit #sisa_piutang").val(formatNumber(sisa_piutang));
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