@extends('layouts/app')
@section('css')
<link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/responsive.dataTables.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
<?php 
    date_default_timezone_set('Asia/Jakarta');
    loadHelper('format,url,akses'); 
    date_default_timezone_set('Asia/Jakarta');
?>
<div class="jumbotron bg-dark text-white">
  <h3>Griya Kencana Lestari</h3>
  <p>Jl. Lingkar Selatan II RT. 023 <br>Kec. Paal Merah Kota Jambi <br>No. Handphone: 0812 7829 8080</p>
</div>
@endsection

@section('js')
<script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('js/jquery.form.min.js')}}"></script>
<script type="text/javascript">
$(".modal").on('hidden.bs.modal', function(){
    $(".form").trigger("reset");
    $(".error").addClass("d-none");
    $(".submit").removeAttr("disabled");
});
</script>
@endsection