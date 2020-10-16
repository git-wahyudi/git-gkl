<div class="form-group row">
    <label class="col-sm-4 col-form-label">{{$label}} @if($required==true)<span class="required  text-danger">*</span>@endif</label>
    <div class="col-sm-8">
	  <select class="form-control select2 {{$class}}" name="{{$fieldname}}" id="{{$fieldname}}">
	  		<option value="">[Pilihan]</option>
	  	@if($data)
	  	@foreach($data as $d)
	  		<option value="{{$d->value}}">{{$d->text}}</option>
	  	@endforeach
	  	@endif
	  </select>
	  <p class="text-danger error {{$fieldname}}Error" style="font-size: 12px; margin: 0px;"></p>
    </div>
</div>