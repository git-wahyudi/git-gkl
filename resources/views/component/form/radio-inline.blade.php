<div class="form-group row">
    <label class="col-sm-4 col-form-label">{{$label}} @if($required==true)<span class="required text-danger">*</span>@endif</label>
    <div class="col-sm-8">
    	@foreach($data as $d)
			<label class="ui-radio ui-radio-inline"><input type="radio" value="{{$d->value}}" name="{{$fieldname}}" id="{{$fieldname}}" @if($checked==true) checked="" @endif><span class="input-span"></span>{{$d->text}}</label>
		@endforeach  
		<p class="text-danger error {{$fieldname}}Error" style="font-size: 12px; margin: 0px;"></p>
    </div>
</div>