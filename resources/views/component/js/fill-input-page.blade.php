@if($tag=='input') $("#{{$form}} #{{$id}}").val({{$name}}); @endif
@if($tag=='tanggal') 
	var tgl = {{$name}}.split('-');
	$("#{{$form}} #{{$id}}").val(tgl[2]+'-'+tgl[1]+'-'+tgl[0]); 
@endif
@if($tag=='select') $("#{{$form}} #{{$id}}").val({{$name}}).trigger('change');@endif
@if($tag=='hidden') $("#{{$form}} #{{$id}}").val({{$name}}); @endif
@if($tag=='radio') $("#{{$form}} input[name={{$id}}][value=" + {{$name}} +"]").prop('checked',true); @endif
