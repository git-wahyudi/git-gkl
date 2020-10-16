<div class="form-group row">
    <label class="col-sm-4 col-form-label">{{$label}} @if($required==true)<span class="required  text-danger">*</span>@endif</label>
    <div class="col-sm-8">
    	{{Form::select($fieldname, $data, null, ['id'=>$fieldname,'class' => 'form-control select2'])}}
    	<p class="text-danger error {{$fieldname}}Error" style="font-size: 12px; margin: 0px;"></p>
    </div>
</div>
