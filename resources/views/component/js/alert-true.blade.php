$(document).ready(function(){
    setTimeout(function() {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            showMethod: 'slideDown',
            positionClass: 'toast-bottom-right',
            timeOut: 4000
        };
        toastr.success(data.success, 'Berhasil!');

    }, 700);
})