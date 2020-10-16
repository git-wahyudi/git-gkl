$('.error').addClass('d-none');
var errors = data.responseJSON;
if($.isEmptyObject(errors) == false) {
    $.each(errors.errors,function (key, value) {
    	var key = key.replace('.','');
        var ErrorID = '.' + key +'Error';
        $(ErrorID).removeClass("d-none");
        $(ErrorID).text(value)
    })
}