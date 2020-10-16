@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
{{Html::bsHomeOpenMenu('Master Data Project','tambah-data','Tambah Data')}}
	<table id="table" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
            <tr>
                <th style="width: 10px; text-align: center;">No</th>
                <th style="width: 300px;">Nama Project</th>
                <th>Alamat</th>
                <th style="width: 100px">Kavling</th>
                <th style="text-align: center;width: 80px;">Aksi</th>
            </tr>
        </thead>
    </table>
{{Html::bsHomeClose()}}
@endsection

@section('modal')

{{Html::bsModalOpenLg('master-data-project/add', 'tambah-data', 'Tambah Data')}}
    {{Form::bsText('project', 'Nama Project', '', 'true', '')}}
    {{Form::bsTextArea('alamat', 'Alamat', '', 'true', '')}}
{{Html::bsModalClose('Simpan')}}


@if(ucu())
{{Html::bsModalOpenLg('master-data-project/update', 'update-data', 'Ubah Data')}}
    {{form::bsHidden('uuidU')}}
    {{Form::bsText('projectU', 'Nama Project', '', 'true', '')}}
    {{Form::bsTextArea('alamatU', 'Alamat', '', 'true', '')}}
{{Html::bsModalClose('Ubah')}}
@endif()

@if(ucd())
{{Html::bsModalOpenLg('master-data-project/delete', 'delete-data', 'Hapus Data')}}
    {{form::bsHidden('uuidD')}}
    {{Form::bsRoText('projectD', 'Nama Project', '', 'true', '')}}
    {{Form::bsRoTextArea('alamatD', 'Alamat', '', 'true', '')}}
{{Html::bsModalClose('Hapus')}} 
@endif()
@endsection()

@section('js')
<script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('js/jquery.form.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#table").DataTable({
            processing: true,
            responsive: true,
            serverSide: true,
            bLengthChange: false,
            order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
            ajax: "{{url_admin('master-data-project/datatable')}}",
            columns: [
                { data: 'DT_RowIndex',orderable: false,searchable: false,sClass:'text-center'},
                { data: 'project'},
                { data: 'alamat'},
                { data: 'list_project'},
                { data: 'action', orderable: false, searchable: false, sClass:'text-center'}
            ]
        });

        {{Html::jsShowModal('tambah-data')}}
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('tambah-data')}}

        {{Html::jsShowModal('update-data')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('master-data-project/get-record')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('uuidU','input','$uuid')}}
                    {{Html::jsValueForm('projectU','input','data.project')}}
                    {{Html::jsValueForm('alamatU','input','data.alamat')}}
                },
                error:function(data){
                    $("#modal-update-data").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('update-data')}}

        {{Html::jsShowModal('delete-data')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('master-data-project/get-record')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('uuidD','input','$uuid')}}
                    {{Html::jsValueForm('projectD','input','data.project')}}
                    {{Html::jsValueForm('alamatD','input','data.alamat')}}
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
            $(".submit").removeAttr("disabled");
        });
	});
</script>
@endsection