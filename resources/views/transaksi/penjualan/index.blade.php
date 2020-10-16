@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2-bootstrap.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/bootstrap-datepicker.css')}}" rel="stylesheet" />
<link href="{{asset('css/jquery-ui.css')}}" rel="stylesheet" />
@endsection
@section('content')
{{Html::bsHomeOpenMenu('Transaksi Penjualan','tambah-data','Tambah Data')}}
	<table id="table" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
            <tr>
                <th style="width: 10px; text-align: center;">No</th>
                <th style="width: 100px;">No Kontrak</th>
                <th>Nama</th>
                <th>Project</th>
                <th style="width: 100px;">Tipe Penjualan</th>
                <th style="width: 100px">Tanggal</th>
                <th style="width: 100px">Status</th>
                <th style="width: 50px">Data</th>
                <th style="text-align: center;width: 80px;">Aksi</th>
            </tr>
        </thead>
    </table>
{{Html::bsHomeClose()}}
@endsection

@section('modal')

{{Html::bsModalOpenLg('penjualan/add', 'tambah-data', 'Tambah Data')}}
    {{Form::bsSelect2('penjual', 'Penjual', $o, 'true', '')}}
    {{Form::bsSelect2('konsumen', 'Pembeli', $c, 'true', '')}}
    {{Form::bsSelect2('project', 'Project', $p, 'true', '')}}
    {{Form::bsText('tanggal', 'Tanggal', date('d-m-Y'), 'true', 'tanggal datepicker')}}
    {{Form::bsSelect('tipe', 'Tipe Penjualan',[''=>'[Pilihan]','Kredit'=>'Kredit','Cash'=>'Cash','Cash Tempo'=>'Cash Tempo'],'true','')}}
    {{Form::bsText('saksi1','Saksi Pihak Pertama','','true','')}}
    {{Form::bsText('saksi2','Saksi Pihak Kedua','','true','')}}
{{Html::bsModalClose('Simpan')}}


@if(ucu())
{{Html::bsModalOpenLg('penjualan/update', 'update-data', 'Ubah Data')}}
    {{form::bsHidden('uuidU')}}
    {{Form::bsSelect2('penjualU', 'Penjual', $o, 'true', '')}}
    {{Form::bsSelect2('konsumenU', 'Pembeli', $c, 'true', '')}}
    {{Form::bsSelect2('projectU', 'Project', $p, 'true', '')}}
    {{Form::bsText('tanggalU', 'Tanggal','', 'true', 'tanggal datepicker')}}
    {{Form::bsSelect('tipeU', 'Tipe Penjualan',[''=>'[Pilihan]','Kredit'=>'Kredit','Cash'=>'Cash','Cash Tempo'=>'Cash Tempo'],'true','')}}
    {{Form::bsText('saksi1U','Saksi Pihak Pertama','','true','')}}
    {{Form::bsText('saksi2U','Saksi Pihak Kedua','','true','')}}
{{Html::bsModalClose('Ubah')}}
@endif()

@if(ucd())
{{Html::bsModalOpenLg('penjualan/delete', 'delete-data', 'Hapus Data')}}
    {{form::bsHidden('uuidD')}}
    {{Form::bsRoText('penjualD', 'Penjual', '', 'true', '')}}
    {{Form::bsRoText('konsumenD', 'Pembeli', '', 'true', '')}}
    {{Form::bsRoText('projectD', 'Project', '', 'true', '')}}
    {{Form::bsRoText('tanggalD', 'Tanggal','', 'true', '')}}
    {{Form::bsRoText('tipeD', 'Tipe Penjualan','','true','')}}
    {{Form::bsRoText('saksi1D','Saksi Pihak Pertama','','true','')}}
    {{Form::bsRoText('saksi2D','Saksi Pihak Kedua','','true','')}}
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
		$("#table").DataTable({
            processing: true,
            responsive: true,
            serverSide: true,
            bLengthChange: false,
            order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
            ajax: "{{url_admin('penjualan/datatable')}}",
            columns: [
                { data: 'DT_RowIndex',orderable: false,searchable: false,sClass:'text-center'},
                { data: 'no_transaksi'},
                { data: 'nama'},
                { data: 'project'},
                { data: 'tipe'},
                { data: 'tgl'},
                { data: 'status'},
                { data: 'detail'},
                { data: 'action', orderable: false, searchable: false, sClass:'text-center'}
            ]
        });

        {{Html::jsShowModal('tambah-data')}}
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('tambah-data')}}

        {{Html::jsShowModal('update-data')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('penjualan/get-record')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('uuidU','input','$uuid')}}
                    {{Html::jsValueForm('penjualU','select','data.owner_id')}}
                    {{Html::jsValueForm('konsumenU','select','data.customer_id')}}
                    {{Html::jsValueForm('projectU','select','data.project_id')}}
                    {{Html::jsValueForm('tanggalU','date','data.tgl_penjualan')}}
                    {{Html::jsValueForm('tipeU','select','data.tipe')}}
                    {{Html::jsValueForm('saksi1U','input','data.saksi_pertama')}}
                    {{Html::jsValueForm('saksi2U','input','data.saksi_kedua')}}
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
                url:"{{url_admin('penjualan/get-record')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('uuidD','input','$uuid')}}
                    {{Html::jsValueForm('penjualD','input','data.o_nama')}}
                    {{Html::jsValueForm('konsumenD','input','data.nama')}}
                    {{Html::jsValueForm('projectD','input','data.project')}}
                    {{Html::jsValueForm('tanggalD','date','data.tgl_penjualan')}}
                    {{Html::jsValueForm('tipeD','input','data.tipe')}}
                    {{Html::jsValueForm('saksi1D','input','data.saksi_pertama')}}
                    {{Html::jsValueForm('saksi2D','input','data.saksi_kedua')}}
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