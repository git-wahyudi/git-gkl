@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet">
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet">
<link href="{{asset('css/select2-bootstrap.min.css')}}" rel="stylesheet">
@endsection
@section('content')
<?php 
	date_default_timezone_set('Asia/Jakarta');
    loadHelper('format,url,akses'); 
    date_default_timezone_set('Asia/Jakarta');
?>
<div class="ibox animated fadeInDown">
    <div class="ibox-head">
        <div class="ibox-title"><i class="fa fa-clone mr-2"></i>Role : {{$role->nama_role}}</div>
    </div>
    <div class="ibox-body">
        <div class="table-responsive">
	        @if(ucc())
	        <div style="margin-bottom: -28px;">
	            <a href="{{url_admin('pengaturan-roles')}}" class="btn btn-danger text-white" style="z-index: 10;"><i class="fa fa-arrow-circle-o-left mr-2"></i>Kembali</a>
	            <a type="button" class="btn btn-success text-white" data-toggle="modal" data-target="#modal-tambah-role-menu" style="z-index: 10;"><i class="fa fa-plus-square-o mr-2"></i>Tambah Role Menu</a>
	        </div>
	        @endif
		    <table id="role_menu" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
		        <thead>	
					<tr>
						<th style="width: 30px;">No</th>
						<th>Group Menu</th>
						<th>Nama Menu</th>
						<th style="width: 40px;">Create</th>
						<th style="width: 40px;">Update</th>
						<th style="width: 40px;">Delete</th>
						<th style="width: 50px;text-align: center;">Aksi</th>
					</tr>
				</thead>
			</table>
		</div>
    </div>
</div>
@endsection()

<?php 
$option_yes_no = json_decode(json_encode(array(["value"=>0, "text"=>"No"] , ["value"=>1, "text"=>"Yes"])));
?>

@section('modal')
@if(ucc())
{{Html::bsModalOpen('pengaturan-roles/'.$role->uuid.'/add', 'tambah-role-menu', 'Tambah Role Menu')}}
	{{Form::bsRoText('group_menu', 'Group Menu', $role->nama_role, '')}}
	{{Form::bsSelect2('id_menu', 'Nama Menu', $list_menu, 'true', 'select2')}}
	{{Form::bsRadioInline('create', 'Allow Create', $option_yes_no, 'true', 'true')}}
	{{Form::bsRadioInline('update', 'Allow Update', $option_yes_no, 'true', 'true')}}
	{{Form::bsRadioInline('delete', 'Allow Delete', $option_yes_no, 'true', 'true')}}
{{Html::bsModalClose('Simpan')}}
@endif()

@if(ucu())
{{Html::bsModalOpen('pengaturan-roles/'.$role->uuid.'/update', 'edit-role-menu', 'Edit Role Menu')}}
	{{Form::bsHidden('uuid_roleU', '')}}
	{{Form::bsRoText('group_menuU', 'Group Menu', $role->nama_role, '')}}
	{{Form::bsSelect2('id_menuU', 'Nama Menu', $list_menu, 'true', 'select2')}}
	{{Form::bsRadioInline('createU', 'Allow Create', $option_yes_no, 'true','')}}
	{{Form::bsRadioInline('updateU', 'Allow Update', $option_yes_no, 'true','')}}
	{{Form::bsRadioInline('deleteU', 'Allow Delete', $option_yes_no, 'true','')}}
{{Html::bsModalClose('Ubah')}}
@endif()

@if(ucd())
{{Html::bsModalOpen('pengaturan-roles/'.$role->uuid.'/delete', 'hapus-role-menu', 'Hapus Role Menu')}}
	{{Form::bsHidden('uuid_roleD', '')}}
	{{Form::bsRoText('id_menuD', 'Nama Menu','', '')}}
{{Html::bsModalClose('Hapus')}}
@endif()
@endsection()

@section('js')
<script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('js/jquery.form.min.js')}}"></script>
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#role_menu").DataTable({
			processing: true,
			serverSide: true,
			bLengthChange: false,
			order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
			ajax: "{{url_admin('pengaturan-roles')}}"+"/"+"{{$role->uuid}}"+"/"+"deep-datatable",
			columns: [
				{ data: 'DT_RowIndex', sClass:'text-center'},
				{ data: 'role.nama_role'},
				{ data: 'menu.nama_menu'},
				{ data: 'create'},
				{ data: 'update'},
				{ data: 'delete'},
				{ data: 'action', searchable: false, orderable: false, sClass:'text-center'}
			]
		});

		{{Html::jsShowModal('tambah-role-menu')}}
		{{Html::jsCloseModal()}}
		{{Html::jsSubmitModal('tambah-role-menu')}}

		{{Html::jsShowModal('edit-role-menu')}}
			$uuid = $(e.relatedTarget).data('uuid');
			$.ajax({
                url:"{{url_admin('pengaturan-roles/get-data')}}/"+$uuid,
                success:function(data){
					{{Html::jsValueForm('uuid_roleU','input','$uuid')}}
					{{Html::jsValueForm('old_nameU','input','data.menu_id')}}
					{{Html::jsValueForm('id_menuU','select','data.menu_id')}}
					{{Html::jsValueForm('createU','radio','data.a_create')}}
					{{Html::jsValueForm('updateU','radio','data.a_update')}}
					{{Html::jsValueForm('deleteU','radio','data.a_delete')}}
				},
                error:function(data){
                    $("#modal-edit-role-menu").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
		{{Html::jsCloseModal()}}
		{{Html::jsSubmitModal('edit-role-menu')}}


		{{Html::jsShowModal('hapus-role-menu')}}
			$uuid = $(e.relatedTarget).data('uuid');
			$.ajax({
                url:"{{url_admin('pengaturan-roles/get-data')}}/"+$uuid,
                success:function(data){
					{{Html::jsValueForm('uuid_roleD','input','$uuid')}}
					{{Html::jsValueForm('id_menuD','input','data.menu.nama_menu')}}
				},
                error:function(data){
                    $("#modal-hapus-role-menu").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
		{{Html::jsCloseModal()}}
		{{Html::jsSubmitModal('hapus-role-menu')}}

		$(".modal").on('hidden.bs.modal', function(){
            $(".form").trigger("reset");
            $(".error").addClass("d-none");
            $("select.select2").select2({ allowClear: true });
            $(".submit").removeAttr("disabled");
        });
	});
</script>
@endsection()