$(".modal").on('hidden.bs.modal', function(){
    $(".form").trigger("reset");
    $(".error").addClass("d-none");
    $("select.select2").select2({ allowClear: true });
    $(".clear option[value!='']").remove();
    $(".submit").removeAttr("disabled");
});