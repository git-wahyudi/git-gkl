@extends('layouts.app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2-bootstrap.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/bootstrap-datepicker.css')}}" rel="stylesheet" />
<link href="{{asset('css/jquery-ui.css')}}" rel="stylesheet" />
<style type="text/css">
	table.dataTable {
		border-collapse: collapse;
	}

	table.dataTable th {
	 -webkit-box-sizing:content-box;
	 box-sizing:content-box;
	 border-top:0px !important;
	}
</style>
@endsection
@section('content')
<?php 
	date_default_timezone_set('Asia/Jakarta');
    loadHelper('format,url,akses');
?>
<div class="ibox animated fadeInDown">
    <div class="ibox-head">
        <div class="ibox-title"><i class="fa fa-clone mr-2"></i>Detail Transaksi Kredit {{$p->no_transaksi}}</div>
    </div>
    <div class="ibox-body">
        <div class="table-responsive">
        	<div class="row">
			    <div class="col-md-6">
			        {{Form::bsRoText('konsumen','Nama',$p->nama,'','')}}
			        {{Form::bsRoText('project', 'Project', $p->project, '', '')}}
			    </div>
			    <div class="col-md-6">
			        {{Form::bsRoText('tgl_pjl','Tanggal Transaksi',toDateDisplay($p->tgl_penjualan),'true','tanggal')}}
			        {{Form::bsRoText('tp','Tipe Penjualan',$p->tipe,'','')}}
			    </div>
			</div>
			<table class="table dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
				<tbody>
					<tr style="background: #e9ecef;border-color: red !important;">
						<td class="text-center" style="width:30px;">No</td>
						<td>No Kavling</td>
						<td class="text-right" style="width: 100px;">Luas /M2</td>
						<td class="text-right" style="width: 120px;">Harga /M2</td>
                        <td class="text-right" style="width: 120px;">Total</td>
                        <td class="text-right" style="width: 120px;">Potongan</td>
						<td class="text-right" style="width: 120px;">Grand Total</td>
					</tr>
					@foreach($p->penjualan_detail as $pd)
					<tr>
						<td class="text-center">{{$pd->incrementing}}</td>
						<td>{{$pd->no_kavling}}</td>
						<td class="text-right">{{toMoney($pd->luas)}}</td>
						<td class="text-right">{{toMoney($pd->harga)}}</td>
						<td class="text-right">{{toMoney($pd->total_harga)}}</td>
						<td class="text-right">{{toMoney($pd->disc_value)}}</td>
						<td class="text-right">{{toMoney($pd->total_harga-$pd->disc_value)}}</td>
					</tr>
					@endforeach
				</tbody>
				<tfoot>
					<tr style="background: #e9ecef;">
						<td colspan="7">Detail Kredit</td>
					</tr>
					<tr>
						<td class="text-right" colspan="3">Total Harga Kavling</td>
						<td class="text-right">{{toMoney($p->total_harga-$p->potongan)}}</td>
						<td class="text-right" colspan="2">Angsuran</td>
						<td class="text-right">{{toMoney($p->angsuran)}}</td>
					</tr>
					<tr>
						<td class="text-right" colspan="3">Uang Muka</td>
						<td class="text-right">{{toMoney($p->uang_muka)}}</td>
						<td class="text-right" colspan="2">Tenor</td>
						<td class="text-right">{{$p->tenor}} Bulan</td>
					</tr>
					<tr>
						<td class="text-right" colspan="3">Piutang</td>
						<td class="text-right">{{toMoney($p->total_harga-$p->potongan-$p->uang_muka)}}</td>
						<td class="text-right" colspan="2">Tanggal JT</td>
						<td class="text-right">{{toDateDisplay($p->tgl_bayar)}}</td>
					</tr>
				</tfoot>
			</table>			

	        <div>
	            <a href="{{url_admin('history-kredit')}}" class="btn btn-danger text-white"><i class="fa fa-arrow-circle-o-left mr-2"></i>Kembali</a>
	        </div>

	        <table id="table_kredit"></table>
		</div>
    </div>
</div>
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
		function formatNumber(num) {
	        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')
	    }

        $("#table_kredit").DataTable({
			processing: true,
			serverSide: true,
			bLengthChange: false,
			bFilter: false,
			bInfo: false,
			bPaginate: false,
			order: [],
            aoColumnDefs: [
                { orderable: false, targets: '_all' }
            ],
			ajax: "{{url_admin('history-kredit')}}/{{$p->uuid}}/datatable",
			columns: [
				{ data: 'data'}
			]
		});
	});
</script>
@endsection()