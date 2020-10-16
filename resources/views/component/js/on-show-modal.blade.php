$("#modal-{{$id}}").on('shown.bs.modal', function(e){
	$(this).find(":input:not(:button):visible:enabled:not([readonly]):first").focus();