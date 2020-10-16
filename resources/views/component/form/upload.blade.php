<div class="form-group row">
    <label class="col-sm-4 col-form-label">{{$label}} @if($required==true)<span class="text-danger">*</span>@endif</label>
    <div class="col-sm-8">
    <input type="file" class="form-control" id="{{$fieldname}}" name="{{$fieldname}}" accept="{{$file_type}}">
    <p class="text-danger error {{$fieldname}}Error" style="font-size: 12px; margin: 0px;"></p>
    </div>
</div>