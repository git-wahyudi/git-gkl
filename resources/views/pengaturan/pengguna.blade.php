@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet">
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet">
<link href="{{asset('css/select2-bootstrap.min.css')}}" rel="stylesheet">
@endsection
@section('content')
{{Html::bsHomeOpenMenu('Pengaturan Pengguna','tambah-users','Tambah Pengguna')}}
	<table id="pengguna" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
			<tr>
				<th style="width: 50px;">No</th>
				<th>Username</th>
				<th>Nama Pengguna</th>
				<th>Role</th>
				<th>Status</th>
				<th style="width: 80px;text-align: center;">Aksi</th>
			</tr>
		</thead>
	</table>
{{Html::bsHomeClose()}}
@endsection()

@section('modal')
@if(ucc())
{{Html::bsModalOpen('pengaturan-data-pengguna/add','tambah-users', 'Tambah Pengguna')}}
	{{Form::bsText('username', 'Username', '', 'true','')}}
	{{Form::bsPassword('password', 'Password', '', 'true', '')}}
	{{Form::bsText('nama', 'Nama Pengguna', '', 'true', '')}}
	{{Form::bsSelect2('role','Role',$role,'true','')}}
	{{Form::bsSelect('status','Status',['1' => 'Enable', '0' => 'Disable'],'true','')}}
{{Html::bsModalClose('Simpan')}}
@endif()

@if(ucu())
{{Html::bsModalOpen('pengaturan-data-pengguna/update', 'update-users', 'Update Pengguna')}}
	{{Form::bsHidden('uuidU')}}
	{{Form::bsRoText('usernameU', 'Username', '', 'true','')}}
	{{Form::bsText('namaU', 'Nama Pengguna', '', 'true', '')}}
	{{Form::bsSelect2('roleU','Role',$role,'true','')}}
	{{Form::bsSelect('statusU','Status',['1' => 'Enable', '0' => 'Disable'],'true','')}}
{{Html::bsModalClose('Ubah')}}
@endif()

@if(ucd())
{{Html::bsModalOpen('pengaturan-data-pengguna/delete', 'delete-users', 'Hapus Pengguna')}}
	{{Form::bsHidden('uuidD')}}
	{{Form::bsRoText('usernameD', 'Username', '', '')}}
{{Html::bsModalClose('Hapus')}}
@endif()

@if(ucu())
{{Html::bsModalOpen('pengaturan-data-pengguna/reset-password', 'reset-password', 'Reset Password')}}
	{{Form::bsHidden('uuidR')}}
	<div class="alert alert-warning text-center"> Reset password menjadi "admin123"</div>
	{{Form::bsRoText('usernameR', 'Username', '', '')}}
{{Html::bsModalClose('Reset')}}
@endif()
@endsection()

@section('js')
<script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('js/jquery.form.min.js')}}"></script>
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#pengguna").DataTable({
			processing: true,
			responsive: true,
			serverSide: true,
			bLengthChange: false,
			order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
			ajax: "{{url_admin('pengaturan-data-pengguna/datatable')}}",
			columns: [
				{ data: 'DT_RowIndex',sClass:'text-center'},
				{ data: 'username'},
				{ data: 'nama'},
				{ data: 'role'},
				{ data: 'status'},
				{ data: 'action', searchable: false, orderable: false, sClass:'text-center'}
			]
		});

		{{Html::jsShowModal('tambah-users')}}
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('tambah-users')}}


        {{Html::jsShowModal('update-users')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('pengaturan-data-pengguna/get-record')}}/"+$uuid,
                success:function(data){
	                {{Html::jsValueForm('uuidU','input','$uuid')}}
	                {{Html::jsValueForm('usernameU','input','data.username')}}
	                {{Html::jsValueForm('namaU','input','data.nama')}}
	                {{Html::jsValueForm('roleU','select','data.user_role.role.uuid')}}
	                {{Html::jsValueForm('statusU','select','data.status')}}
	            },
                error:function(data){
                    $("#modal-update-users").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('update-users')}}

        {{Html::jsShowModal('delete-users')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('pengaturan-data-pengguna/get-record')}}/"+$uuid,
                success:function(data){
	                {{Html::jsValueForm('uuidD','input','$uuid')}}
	                {{Html::jsValueForm('usernameD','input','data.username')}}
	            },
                error:function(data){
                    $("#modal-delete-users").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('delete-users')}}

        {{Html::jsShowModal('reset-password')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('pengaturan-data-pengguna/get-record')}}/"+$uuid,
                success:function(data){
	            	{{Html::jsValueForm('uuidR','input','$uuid')}}
	            	{{Html::jsValueForm('usernameR','input','data.username')}}
	            },
                error:function(data){
                    $("#modal-reset-password").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('reset-password')}}

        $(".modal").on('hidden.bs.modal', function(){
            $(".form").trigger("reset");
            $(".error").addClass("d-none");
            $("select.select2").select2({ allowClear: true });
            $(".submit").removeAttr("disabled");
        });
	});
</script>
@endsection()
