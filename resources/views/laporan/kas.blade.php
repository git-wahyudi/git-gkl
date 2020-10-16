@extends('layouts.app')
@section('css')
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2-bootstrap.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
{{Html::bsHomeOpen('Laporan Kas')}}
    <div class="row">
        <div class="col-md-3">
        	<div class="form-group row">
			    <label class="col-md-4 col-form-label">Periode</label>
			    <div class="col-md-8">
			    	<select class="form-control select2 default" id="bulan">
			    		<option value="">[Pilihan]</option>
			    		<option value="1">Januari</option>
			    		<option value="2">Februari</option>
			    		<option value="3">Maret</option>
			    		<option value="4">April</option>
			    		<option value="5">Mei</option>
			    		<option value="6">Juni</option>
			    		<option value="7">Juli</option>
			    		<option value="8">Agustus</option>
			    		<option value="9">September</option>
			    		<option value="10">Oktober</option>
			    		<option value="11">November</option>
			    		<option value="12">Desember</option>
			    	</select>
			  	</div>
			</div>
        </div>
        <div class="col-md-2">
        	<div class="form-group row">
			    <label class="col-md-4 col-form-label">Tahun</label>
			    <div class="col-md-8">
			    	<select class="form-control select2 default" id="tahun">
			    		<option value="">[Pilihan]</option>
			    		@for($i=2020; $i<=$y; $i++)
			    		<option value="{{$i}}">{{$i}}</option>
			    		@endfor
			    	</select>
			  	</div>
			</div>
        </div>
        <div class="col-md-4">
        	<div class="form-group row">
			    <label class="col-md-5 col-form-label">Ukuran Kertas</label>
			    <div class="col-md-6">
			    	<select class="form-control select2" id="ukuran">
			    		<option value="Potrait">Potrait</option>
			    		<option value="Landscape">Landscape</option>
			    	</select>
			  	</div>
			</div>
        </div>
        <div class="col-md-3">
        	<button type="button" id="cari" class="btn btn-primary mb-1">Lihat Data</button>
        </div>
    </div>
{{Html::bsHomeClose()}}
@endsection

@section('js')
<script src="{{asset('js/select2.min.js')}}"></script>
<script type="text/javascript">
	$(document).ready(function(){
        $(".default").val("").trigger('change');
        $("#ukuran").val("Potrait").trigger('change');

		$("#cari").click(function(){
			var bulan = $("#bulan").val();
	        var tahun = $("#tahun").val();
	        var ukuran = $("#ukuran").val();
	        if(bulan == "" || tahun == "" || ukuran == ""){
	        	{{Html::jsAlertFail()}}
	        }else {
	        	window.open( "{{url_admin('laporan-kas')}}/"+bulan+"/"+tahun+"/"+ukuran );
	        }
		});
	});
</script>
@endsection