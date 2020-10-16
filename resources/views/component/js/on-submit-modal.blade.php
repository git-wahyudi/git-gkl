$('#{{$id}}').ajaxForm({
    beforeSubmit:function(){
        $(".submit").attr("disabled", true);
    },
    success:function(data){
        $("#modal-{{$id}}").modal('hide');
        $('.table').DataTable().ajax.reload();
        if (data.success){
            {{Html::jsAlertTrue()}}
        }else{
            {{Html::jsAlertFalse()}}
        }
    },
    error:function(data){
        $(".submit").removeAttr("disabled");
        {{Html::jsValidate()}}
    }
}); 
