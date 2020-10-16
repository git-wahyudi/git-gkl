@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
{{Html::bsHomeOpen('Transaksi Angsuran')}}
	<div class="row">
        <div class="col-md-12">
        	<div class="form-group row">
			    <label class="col-md-2 col-form-label">Cari No Kontrak / Nama</label>
			    <div class="col-md-4">
			    	<input type="text" class="form-control" name="param" id="param" type="text" autocomplete="off" autofocus>
			  	</div>
			</div>
        </div>
        <!-- <div class="col-md-3">
        	<button type="button" id="cari" class="btn btn-success">Cari Data</button>
        </div> -->
    </div>
	<table id="table" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
        <thead>
            <tr>
                <th style="width: 10px; text-align: center;">No</th>
                <th style="width: 120px;">No Kontrak</th>
                <th>Nama</th>
                <th>Project</th>
                <th style="width: 120px;">Total Piutang</th>
                <th style="width: 120px;">Potongan Pelunasan</th>
                <th style="width: 120px">Total Bayar</th>
                <th style="width: 120px">Sisa Piutang</th>
                <th style="width: 50px" class="text-center">Data</th>
            </tr>
        </thead>
    </table>
{{Html::bsHomeClose()}}
@endsection


@section('js')
<script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/dataTables.responsive.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#table").DataTable({
            processing: true,
            responsive: true,
            serverSide: true,
            bLengthChange: false,
            bFilter: false,
            order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
            ajax: {
            	url : "{{url_admin('angsuran/datatable')}}",
            	data: function (d) {
	            	d.param = $('#param').val();
	            }
            },
            columns: [
                { data: 'DT_RowIndex',orderable: false,searchable: false,sClass:'text-center'},
                { data: 'no_transaksi'},
                { data: 'nama'},
                { data: 'project'},
                { data: 'total',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
                { data: 'pot_angsuran',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
                { data: 'total_angsuran',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
                { data: 'sisa_piutang',render: $.fn.dataTable.render.number( '.' ),sClass:'text-right'},
                { data: 'detail', sClass:'text-center'}
            ]
        });

        $('#param').keyup(function(){
        	if($(this).val().length > 3){
	       		$('#table').DataTable().draw(true);
        	}else if($(this).val().length == 0){
        		$('#table').DataTable().draw(true);
        	}

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
	});
</script>
@endsection