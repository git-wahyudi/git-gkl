<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <title>Griya Kencana Lestari</title>
    <!-- core css-->
    <link href="{{asset('vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet" />
    <link href="{{asset('vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" />
    <link href="{{asset('vendors/themify-icons/css/themify-icons.css')}}" rel="stylesheet" />
    <link href="{{asset('css/main.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/animate.css')}}" rel="stylesheet" />
    <link href="{{asset('css/toastr.min.css')}}" rel="stylesheet" />
    @section('css')
    @show()
</head>

<body class="fixed-navbar has-animation fixed-layout">
    <div class="page-wrapper">
        <!-- START HEADER-->
        @include('layouts/top-nav')
        <!-- END HEADER-->
        <!-- START SIDEBAR-->
        @include('layouts/navigation')
        <!-- END SIDEBAR-->
        <div class="content-wrapper">
            <!-- START PAGE CONTENT-->
            <div class="page-content"> 
            	<!-- Main content -->
		        @section('content')
		        @show()   
            </div>
            <!-- END PAGE CONTENT-->
            @include('layouts/footer') 
        </div>
    </div>
    
    <!-- END PAGA BACKDROPS-->
    <!-- Modal -->
    @section('modal')
    @show()

    {{Html::bsModalOpenLg('submit-password', 'ubah-password', 'Ubah Password')}}
        {{Form::bsPassword('lama', 'Password Lama', '', 'true', '')}}
        {{Form::bsPassword('baru1', 'Password Baru', '', 'true', '')}}
        {{Form::bsPassword('baru2', 'Konfirmasi', '', 'true', '')}}
    {{Html::bsModalClose('Simpan')}}

    {{Html::bsModalOpenLg('edit-profile', 'profile', 'Profile')}}
        {{Form::bsHidden('id', encrypt(Auth::user()->id))}}
        {{Form::bsHidden('old_photo', Auth::user()->photo)}}
        {{Form::bsText('nama', 'Nama', '', 'true', '')}}
        {{Form::bsText('telp', 'Telp', '', 'true', '')}}
        <!-- {{Form::bsUpload('photo', 'Photo (.jpg/.png)', 'true', '.jpg, .jpeg, .png')}} -->
    {{Html::bsModalClose('Simpan')}}

   
    <!-- core js-->
    <script src="{{asset('vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('vendors/metisMenu/dist/metisMenu.min.js')}}"></script>
    <script src="{{asset('vendors/jquery-slimscroll/jquery.slimscroll.min.js')}}"></script>
    <script src="{{asset('js/toastr.min.js')}}"></script>
    <script src="{{asset('js/app.min.js')}}"></script>
    @section('js')
    @show()
   
    <script type="text/javascript">
        $(document).ready(function(){
            $("select.select2").select2();
            $('.rupiah').mask('#.##0', { reverse: true });
            $('.angka').mask('#', { reverse: true });
            $('.tanggal').mask('00-00-0000');
            $( ".datepicker" ).datepicker({ dateFormat: 'dd-mm-yy' });

            $('#sidebar-collapse').slimScroll({
                height: '100%',
                railOpacity: '0.9',
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            {{Html::jsShowModal('ubah-password')}}
                $('.content').show();
            {{Html::jsCloseModal()}}
            {{Html::jsSubmitModal('ubah-password')}}

            {{Html::jsShowModal('profile')}}
                $id = $(e.relatedTarget).data('id');
                $.get("{{url('admin/get-record')}}/"+$id, function(data){
                    {{Html::jsValueForm('nama', 'input', 'data.nama')}}
                    {{Html::jsValueForm('telp', 'input', 'data.telp')}}   
                });
            {{Html::jsCloseModal()}}
            {{Html::jsSubmitModal('profile')}}
        });
    </script>
</body>

</html>