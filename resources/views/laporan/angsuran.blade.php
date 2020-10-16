@extends('layouts.app')
@section('css')
<link href="{{asset('css/select2.min.css')}}" rel="stylesheet" />
<link href="{{asset('css/select2-bootstrap.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
{{Html::bsHomeOpen('Laporan Rekap Data Angsuran')}}
    <div class="row">
        <div class="col-md-5">
            <div class="form-group row">
                <label class="col-md-3 col-form-label">Project</label>
                <div class="col-md-9">
                    <select class="form-control select2" id="project">
                        <option value="">[Pilihan]</option>
                        @foreach($p as $p)
                        <option value="{{$p->id}}">{{$p->project}}</option>
                        @endforeach
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
        $(".select2").val("").trigger('change');

        $("#cari").click(function(){
            var project = $("#project").val();
            if(project == ""){
                {{Html::jsAlertFail()}}
            }else {
                window.open( "{{url_admin('laporan-angsuran')}}/"+project );
            }
        });
    });
</script>
@endsection