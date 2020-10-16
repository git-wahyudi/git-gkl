@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
{{Html::bsHomeOpen('Riwayat Kredit')}}
	<div class="row">
        <div class="col-md-12">
        	<div class="form-group row">
			    <label class="col-md-2 col-form-label">Cari No Kontrak / Nama</label>
			    <div class="col-md-4">
			    	<input type="text" class="form-control" name="param" id="param" type="text" autocomplete="off" autofocus>
			  	</div>
			</div>
        </div>
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
            	url : "{{url_admin('history-kredit/datatable')}}",
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
	});
</script>
@endsection