@if($tag=='input') $(".modal-body #{{$id}}").val({{$name}}); @endif
@if($tag=='moneyInt') $(".modal-body #{{$id}}").val(parseInt({{$name}}).toLocaleString().replace(',', '.')); @endif
@if($tag=='money') $(".modal-body #{{$id}}").val({{$name}}.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.')); @endif
@if($tag=='date') 
	var tgl = {{$name}}.split('-');
	$(".modal-body #{{$id}}").val(tgl[2]+'-'+tgl[1]+'-'+tgl[0]); 
@endif
@if($tag=='select') $(".modal-body #{{$id}}").val({{$name}}).trigger('change');@endif
@if($tag=='radio') $(".modal-body input[name={{$id}}][value=" + {{$name}} +"]").prop('checked',true); @endif