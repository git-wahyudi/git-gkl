@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet">
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet">
<link href="{{asset('css/select2-bootstrap.min.css')}}" rel="stylesheet">
@endsection
@section('content')
{{Html::bsHomeOpenMenu('Pengaturan Menu','tambah-menu','Tambah Menu')}}
	<table id="menu" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
            <tr>
                <th style="width: 10px; text-align: center;">No</th>
                <th>Nama Menu</th>
                <th>Menu Induk</th>
                <th>Url</th>
                <th>Urutan</th>
                <th style="text-align: center;width: 80px;">Aksi</th>
            </tr>
        </thead>
    </table>
{{Html::bsHomeClose()}}
@endsection

@section('modal')
@if(ucc())
{{Html::bsModalOpen('pengaturan-menu/add', 'tambah-menu', 'Tambah Menu')}}
    {{Form::bsText('menu', 'Nama Menu', '', 'true', '')}}
    {{Form::bsText('url', 'Url', '', 'true', '')}}
    {{Form::bsSelect2('menu_utama', 'Menu Utama', '', '', 'clear')}}
    {{Form::bsText('urutan', 'Urutan', '', 'true', 'angka')}}
    {{Form::bsText('icon', 'Icon', '', '', '')}}
{{Html::bsModalClose('Simpan')}}
@endif()

@if(ucu())
{{Html::bsModalOpen('pengaturan-menu/update', 'update-menu', 'Ubah Menu')}}
    {{form::bsHidden('uuidU')}}
    {{form::bsText('menuU', 'Menu', '', 'true', '')}}
    {{Form::bsText('urlU', 'Url', '', 'true', '')}}
    {{form::bsSelect2('menu_utamaU', 'Menu Utama', '', '', 'clear')}}
    {{form::bsText('urutanU', 'Urutan', '', 'true', 'angka')}}
    {{form::bsText('iconU', 'Icon', '', '')}}
{{Html::bsModalClose('Ubah')}}
@endif()

@if(ucd())
{{Html::bsModalOpen('pengaturan-menu/delete', 'delete-menu', 'Hapus Menu')}}
    {{form::bsHidden('uuidD')}}
    {{form::bsRoText('menuD', 'Menu', '', '')}}
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
		$("#menu").DataTable({
            processing: true,
            responsive: true,
            serverSide: true,
            bLengthChange: false,
            order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
            ajax: "{{url_admin('pengaturan-menu/datatable')}}",
            columns: [
                { data: 'DT_RowIndex', sClass:'text-center'},
                { data: 'nama_menu'},
                { data: 'menu_induk'},
                { data: 'url'},
                { data: 'urutan'},
                { data: 'action', orderable: false, searchable: false, sClass:'text-center'}
            ]
        });

        {{Html::jsShowModal('tambah-menu')}}
            $.ajax({
                url:"{{url_admin('pengaturan-menu/get-menu')}}",
                success:function(data){
                    $.each(data, function(index, data){
                        $('#menu_utama').append('<option value="'+data.value+'">'+data.text+'</option>');
                    });
                },
                error:function(data){
                    $("#modal-tambah-menu").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('tambah-menu')}}

        {{Html::jsShowModal('update-menu')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('pengaturan-menu/get-record-data')}}/"+$uuid,
                success:function(data){
                    $.each(data.list_induk, function(index, list_induk){
                        $('#menu_utamaU').append('<option value="'+list_induk.value+'">'+list_induk.text+'</option>');
                    });
                    {{Html::jsValueForm('uuidU','input','$uuid')}}
                    {{Html::jsValueForm('menuU','input','data.data.nama_menu')}}
                    {{Html::jsValueForm('urlU','input','data.data.url')}}
                    {{Html::jsValueForm('menu_utamaU','select','data.data.menu_id')}}
                    {{Html::jsValueForm('urutanU','input','data.data.urutan')}}
                    {{Html::jsValueForm('iconU','input','data.data.icon')}}
                },
                error:function(data){
                    $("#modal-update-menu").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('update-menu')}}

        {{Html::jsShowModal('delete-menu')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('pengaturan-menu/get-record')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('uuidD','input','$uuid')}}
                    {{Html::jsValueForm('menuD','input','data.nama_menu')}}
                },
                error:function(data){
                    $("#modal-delete-menu").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('delete-menu')}}
        
        $(".modal").on('hidden.bs.modal', function(){
            $(".form").trigger("reset");
            $(".error").addClass("d-none");
            $("select.select2").select2({ allowClear: true });
            $(".clear option[value!='']").remove();
            $(".submit").removeAttr("disabled");
        });
	});
</script>
@endsection