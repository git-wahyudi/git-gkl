@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet">
@endsection
@section('content')
{{Html::bsHomeOpenMenu('Pengaturan Roles', 'tambah-roles', 'Tambah Roles')}}
    <table id="roles" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
			<tr>
				<th style="width: 50px;">No</th>
				<th>Nama Roles</th>
				<th>List Menu</th>
				<th style="width: 80px;text-align: center;">Aksi</th>
			</tr>
		</thead>
	</table>

{{Html::bsHomeClose()}}
@endsection()

@section('modal')
@if(ucc())
{{Html::bsModalOpen('pengaturan-roles/add','tambah-roles', 'Tambah Roles')}}
	{{Form::bsText('roles', 'Nama Roles', '', 'true','')}}
{{Html::bsModalClose('Simpan')}}
@endif()

@if(ucu())
{{Html::bsModalOpen('pengaturan-roles/update', 'update-roles', 'Ubah Roles')}}
	{{Form::bsHidden('uuidU')}}
	{{Form::bsText('rolesU', 'Nama Roles', '','true', '')}}
{{Html::bsModalClose('Ubah')}}
@endif()

@if(ucd())
{{Html::bsModalOpen('pengaturan-roles/delete', 'delete-roles', 'Hapus Roles')}}
	{{Form::bsHidden('uuidD')}}
	{{Form::bsHidden('id_roleD')}}
	{{Form::bsRoText('rolesD', 'Nama Roles', '', '')}}
{{Html::bsModalClose('Hapus')}}
@endif()
@endsection()

@section('js')
<script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('js/jquery.form.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#roles").DataTable({
            processing: true,
			responsive: true,
            serverSide: true,
            bLengthChange: false,
            order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
            ajax: '{{ url_admin("pengaturan-roles/datatable") }}',
            columns: [
                { data: 'DT_RowIndex', sClass:'text-center'},
                { data: 'nama_role'},
                { data: 'list_menu', 'defaultContent':0},
                { data: 'action', name: 'action', orderable: false, searchable: false, sClass:'text-center'}
            
            ]
        });


        {{Html::jsShowModal('tambah-roles')}}
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('tambah-roles')}}

        {{Html::jsShowModal('update-roles')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('pengaturan-roles/get-record')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('uuidU','input','$uuid')}}
                    {{Html::jsValueForm('rolesU','input','data.nama_role')}}
                },
                error:function(data){
                    $("#modal-update-roles").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('update-roles')}}

        {{Html::jsShowModal('delete-roles')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('pengaturan-roles/get-record')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('uuidD','input','$uuid')}}
                    {{Html::jsValueForm('rolesD','input','data.nama_role')}}
                },
                error:function(data){
                    $("#modal-delete-roles").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('delete-roles')}}

        $(".modal").on('hidden.bs.modal', function(){
            $(".form").trigger("reset");
            $(".error").addClass("d-none");
            $(".submit").removeAttr("disabled");
        });
	});
</script>
@endsection()
