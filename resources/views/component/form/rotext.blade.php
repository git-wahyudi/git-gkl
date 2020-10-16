<div class="form-group row">
    <label class="col-sm-4 col-form-label">{{$label}}</label>
    <div class="col-sm-8">
    <input type="text" class="form-control" id="{{$fieldname}}" name="{{$fieldname}}" 
	  value="{{$value}}" autocomplete="off" readonly="readonly">
	  <p class="text-danger error {{$fieldname}}Error" style="font-size: 12px; margin: 0px;"></p>
    </div>
</div>