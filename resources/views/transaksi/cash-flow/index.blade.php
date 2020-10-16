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
{{Html::bsHomeOpenMenu('Transaksi Cash Flow','tambah-data','Tambah Data')}}
	<table id="table" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
            <tr>
                <th style="width: 10px; text-align: center;">No</th>
                <!-- <th style="width: 100px;">No Transaksi</th> -->
                <th style="width: 100px;">Tanggal</th>
                <th>Keterangan</th>
                <th style="width: 100px;">IN</th>
                <th style="width: 100px">OUT</th>
                <th style="width: 100px">Saldo</th>
                <th style="width: 50px">Data</th>
            </tr>
        </thead>
    </table>
{{Html::bsHomeClose()}}
@endsection

@section('modal')
@if(ucc())
{{Html::bsModalOpenLg('cash-flow/add', 'tambah-data', 'Tambah Data')}}
    {{Form::bsSelect('code', 'Transaksi', ['In'=>'In', 'Out'=>'Out'], 'true', '')}}
    {{Form::bsText('tanggal', 'Tanggal', date('d-m-Y'), 'true', 'tanggal datepicker')}}
    {{Form::bsTextArea('ket', 'Rincian', '', 'true', '')}}
    {{Form::bsText('jumlah', 'Jumlah', '', 'true', 'rupiah')}}
{{Html::bsModalClose('Simpan')}}

{{Html::bsModalOpen('', 'detail', 'Data Detail')}}
    {{Form::bsRoText('ca', 'Created at', '', '', '')}}
    {{Form::bsRoText('cb', 'Created by', '', '', '')}}
{{Html::bsModalClose()}}
@endif()
@endsection()

@section('js')
<script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
<!-- <script src="{{asset('js/dataTables.responsive.min.js')}}"></script> -->
<script src="{{asset('js/jquery.form.min.js')}}"></script>
<script src="{{asset('js/select2.min.js')}}"></script>
<script src="{{asset('js/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js/jquery-ui.js')}}"></script>
<script src="{{asset('js/jquery.mask.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#table").DataTable({
            processing: true,
            // responsive: true,
            serverSide: true,
            bLengthChange: false,
            order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
            ajax: "{{url_admin('cash-flow/datatable')}}",
            columns: [
                { data: 'DT_RowIndex',orderable: false,searchable: false,sClass:'text-center'},
                // { data: 'no_transaksi'},
                { data: 'tanggal'},
                { data: 'keterangan'},
                { data: 'in',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
                { data: 'out',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
                { data: 'balance',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
                { data: 'detail',sClass:'text-center'}
            ]
        });

        {{Html::jsShowModal('tambah-data')}}
        {{Html::jsCloseModal()}}
        {{Html::jsSubmitModal('tambah-data')}}

        {{Html::jsShowModal('detail')}}
            $uuid = $(e.relatedTarget).data('uuid');
            $.ajax({
                url:"{{url_admin('cash-flow/get-detail')}}/"+$uuid,
                success:function(data){
                    {{Html::jsValueForm('ca','input','data.ca')}}
                    {{Html::jsValueForm('cb','input','data.cb')}}
                },
                error:function(data){
                    $("#modal-detail").modal('hide');
                    {{Html::jsAlertFail()}}
                }
            });
        {{Html::jsCloseModal()}}

        $(".modal").on('hidden.bs.modal', function(){
            $(".form").trigger("reset");
            $(".error").addClass("d-none");
            $(".submit").removeAttr("disabled");
            $("select.select2").select2({ allowClear: true });
        });
	});
</script>
@endsection