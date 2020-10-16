@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2-bootstrap.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
{{Html::bsHomeOpenMenu('Master Data Customer','tambah-data','Tambah Data')}}
	<table id="table" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
            <tr>
                <th style="width: 10px; text-align: center;">No</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Alamat</th>
                <th>No Telp/HP</th>
                <th style="text-align: center;width: 80px;">Aksi</th>
            </tr>
        </thead>
    </table>
{{Html::bsHomeClose()}}
@endsection

@section('modal')

{{Html::bsModalOpenLg('master-data-customer/add', 'tambah-data', 'Tambah Data')}}
    {{Form::bsText('nama', 'Nama', '', 'true', '')}}
    {{Form::bsText('nik', 'NIK', '', 'true', '')}}
    {{Form::bsText('ttl', 'Tempat & Tgl Lahir', '', 'true', '')}}
    {{Form::bsSelect('jk','Jenis Kelamin',[''=>'[Pilihan]','Laki-laki'=>'Laki-laki','Perempuan'=>'Perempuan'],'true','')}}
    {{Form::bsTextArea('alamat', 'Alamat', '', 'true', '')}}
    {{Form::bsSelect('agama','Agama',[''=>'[Pilihan]','Islam'=>'Islam','Protestan'=>'Protestan', 'Katolik'=>'Katolik','Hindu'=>'Hindu','Buddha'=>'Buddha','Khonghucu'=>'Khonghucu'],'true','')}}
    {{Form::bsText('pekerjaan', 'Pekerjaan', '', 'true', '')}}
    {{Form::bsText('telp', 'No Telp/HP', '', 'true', '')}}
{{Html::bsModalClose('Simpan')}}


@if(ucu())
{{Html::bsModalOpenLg('master-data-customer/update', 'update-data', 'Ubah Data')}}
    {{form::bsHidden('uuidU')}}
    {{Form::bsText('namaU', 'Nama', '', 'true', '')}}
    {{Form::bsText('nikU', 'NIK', '', 'true', '')}}
    {{Form::bsText('ttlU', 'Tempat & Tgl Lahir', '', 'true', '')}}
    {{Form::bsSelect('jkU','Jenis Kelamin',[''=>'[Pilihan]','Laki-laki'=>'Laki-laki','Perempuan'=>'Perempuan'],'true','')}}
    {{Form::bsTextArea('alamatU', 'Alamat', '', 'true', '')}}
    {{Form::bsSelect('agamaU','Agama',[''=>'[Pilihan]','Islam'=>'Islam','Protestan'=>'Protestan', 'Katolik'=>'Katolik','Hindu'=>'Hindu','Buddha'=>'Buddha','Khonghucu'=>'Khonghucu'],'true','')}}
    {{Form::bsText('pekerjaanU', 'Pekerjaan', '', 'true', '')}}
    {{Form::bsText('telpU', 'No Telp/HP', '', 'true', '')}}
{{Html::bsModalClose('Ubah')}}
@endif()

@if(ucd())
{{Html::bsModalOpen('master-data-customer/delete', 'delete-data', 'Hapus Data')}}
    {{form::bsHidden('uuidD')}}
    {{form::bsRoText('namaD', 'Nama', '', '')}}
    {{form::bsRoText('nikD', 'NIK', '', '')}}
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
            responsive: true,
            serverSide: true,
            bLengthChange: false,
            order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
            ajax: "{{url_admin('master-data-customer/datatable')}}",
            columns: [
                { data: 'DT_RowIndex',orderable: false,searchable: false,sClass:'text-center'},
                { data: 'nama'},
                { data: 'nik'},
                { data: 'alamat'},
                { data: 'telp'},
                { data: 'action', orderable: false, searchable: false, sClass:'text-center'}
            ]
        });

        {{Html::jsShowModal('tambah-data')}}
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('tambah-data')}}

        {{Html::jsShowModal('update-data')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('master-data-customer/get-record')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('uuidU','input','$uuid')}}
                    {{Html::jsValueForm('namaU','input','data.nama')}}
                    {{Html::jsValueForm('nikU','input','data.nik')}}
                    {{Html::jsValueForm('ttlU','input','data.ttl')}}
                    {{Html::jsValueForm('jkU','select','data.jk')}}
                    {{Html::jsValueForm('alamatU','input','data.alamat')}}
                    {{Html::jsValueForm('agamaU','select','data.agama')}}
                    {{Html::jsValueForm('pekerjaanU','input','data.pekerjaan')}}
                    {{Html::jsValueForm('telpU','input','data.telp')}}
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
                url:"{{url_admin('master-data-customer/get-record')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('uuidD','input','$uuid')}}
                    {{Html::jsValueForm('namaD','input','data.nama')}}
                    {{Html::jsValueForm('nikD','input','data.nik')}}
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
            $("select.select2").select2({ allowClear: true });
        });
	});
</script>
@endsection