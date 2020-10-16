@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<?php 
	date_default_timezone_set('Asia/Jakarta');
    loadHelper('format,url,akses');
?>
<div class="ibox animated fadeInDown">
    <div class="ibox-head">
        <div class="ibox-title"><i class="fa fa-clone mr-2"></i>Project {{$p->project}}</div>
    </div>
    <div class="ibox-body">
        <div class="table-responsive">
	        @if(ucc())
	        <div style="margin-bottom: -28px;">
	            <a href="{{url_admin('master-data-project')}}" class="btn btn-danger text-white" style="z-index: 10;"><i class="fa fa-arrow-circle-o-left mr-2"></i>Kembali</a>
	            <a type="button" class="btn btn-success text-white" data-toggle="modal" data-target="#modal-tambah-data" style="z-index: 10;"><i class="fa fa-plus-square-o mr-2"></i>Tambah Project Item</a>
	        </div>
	        @endif
		    <table id="table" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
		        <thead>	
					<tr>
						<th style="width: 30px;">No</th>
						<th>No Kavling</th>
						<th style="width: 100px;">Luas /M2</th>
						<th class="text-right" style="width: 120px;">Harga /M2</th>
						<th class="text-right" style="width: 120px;">Total Harga</th>
						<th style="width: 50px;text-align: center;">Aksi</th>
					</tr>
				</thead>
			</table>
		</div>
    </div>
</div>
@endsection()

@section('modal')
@if(ucc())
{{Html::bsModalOpenLg('master-data-project/'.$p->uuid.'/add', 'tambah-data', 'Tambah Data')}}
	{{Form::bsHidden('uuidP',$p->uuid)}}
	{{Form::bsRoText('project', 'Project', $p->project, '')}}
	{{Form::bsText('no', 'No Kavling','','true','')}}
	{{Form::bsText('luas', 'Luas (M2)','','true','rupiah')}}
	{{Form::bsText('harga', 'Harga /M2','','true','rupiah')}}
{{Html::bsModalClose('Simpan')}}
@endif()

@if(ucu())
{{Html::bsModalOpenLg('master-data-project/'.$p->uuid.'/update', 'update-data', 'Ubah Data')}}
	{{Form::bsHidden('uuidU', '')}}
	{{Form::bsRoText('projectU', 'Project', $p->project, '')}}
	{{Form::bsText('noU', 'No Kavling','','true','')}}
	{{Form::bsText('luasU', 'Luas (M2)','','true','rupiah')}}
	{{Form::bsText('hargaU', 'Harga /M2','','true','rupiah')}}
{{Html::bsModalClose('Ubah')}}
@endif()

@if(ucd())
{{Html::bsModalOpenLg('master-data-project/'.$p->uuid.'/delete', 'delete-data', 'Hapus Data')}}
	{{Form::bsHidden('uuidD', '')}}
	{{Form::bsRoText('projectD', 'Project', $p->project, '')}}
	{{Form::bsRoText('noD', 'No Kavling','','true','')}}
	{{Form::bsRoText('luasD', 'Luas (M2)','','true','rupiah')}}
	{{Form::bsRoText('hargaD', 'Harga /M2','','true','rupiah')}}
{{Html::bsModalClose('Hapus')}}
@endif()
@endsection()

@section('js')
<script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('js/jquery.mask.min.js')}}"></script>
<script src="{{asset('js/jquery.form.min.js')}}"></script>
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#table").DataTable({
			processing: true,
			serverSide: true,
			bLengthChange: false,
			order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
			ajax: "{{url_admin('master-data-project')}}/{{$p->uuid}}/datatable",
			columns: [
				{ data: 'DT_RowIndex', sClass:'text-center'},
				{ data: 'no_kavling'},
				{ data: 'luas',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
				{ data: 'harga',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
				{ data: 'total_harga',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
				{ data: 'action', searchable: false, orderable: false, sClass:'text-center'}
			]
		});

		{{Html::jsShowModal('tambah-data')}}
		{{Html::jsCloseModal()}}
		{{Html::jsSubmitModal('tambah-data')}}

		{{Html::jsShowModal('update-data')}}
			var uuid = $(e.relatedTarget).data('uuid');
			$.ajax({
                url:"{{url_admin('master-data-project')}}/{{$p->uuid}}/get-record/"+uuid,
                success:function(data){
					{{Html::jsValueForm('uuidU','input','uuid')}}
					{{Html::jsValueForm('noU','input','data.no_kavling')}}
					{{Html::jsValueForm('luasU','money','data.luas')}}
					{{Html::jsValueForm('hargaU','money','data.harga')}}
				},
                error:function(data){
                    $("#modal-update-data").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
		{{Html::jsCloseModal()}}
		{{Html::jsSubmitModal('update-data')}}


		{{Html::jsShowModal('delete-data')}}
			var uuid = $(e.relatedTarget).data('uuid');
			$.ajax({
                url:"{{url_admin('master-data-project')}}/{{$p->uuid}}/get-record/"+uuid,
                success:function(data){
					{{Html::jsValueForm('uuidD','input','uuid')}}
					{{Html::jsValueForm('noD','input','data.no_kavling')}}
					{{Html::jsValueForm('luasD','money','data.luas')}}
					{{Html::jsValueForm('hargaD','money','data.harga')}}
				},
                error:function(data){
                    $("#modal-delete-data").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
		{{Html::jsCloseModal()}}
		{{Html::jsSubmitModal('delete-data')}}

		$(".modal").on('hidden.bs.modal', function(){
            $(".form").trigger("reset");
            $(".error").addClass("d-none");
            $("select.select2").select2({ allowClear: true });
            $(".submit").removeAttr("disabled");
        });
	});
</script>
@endsection()