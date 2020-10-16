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
    <link href="{{asset('vendors/DataTables/datatables.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/main.min.css')}}" rel="stylesheet" />
    <link href="{{asset('css/animate.css')}}" rel="stylesheet" />
    <style type="text/css">
        table.dataTable {
            border-collapse: collapse;
        }

        table.dataTable th {
         -webkit-box-sizing:content-box;
         box-sizing:content-box;
         border-top:0px !important;
        }
    </style>
</head>

<body class="fixed-navbar has-animation fixed-layout">
    <div class="page-wrapper">
        <!-- START HEADER-->
        <header class="header">
            <div class="flexbox flex-1">
                <!-- START TOP-LEFT TOOLBAR-->
                <ul class="nav navbar-toolbar">
                    <li>
                        <a class="nav-link sidebar-toggler js-sidebar-toggler">
                            <img src="{{asset('img/gkl.png')}}" width="60px" />
                        </a>
                    </li>
                    <li class="d-sm-block"> 
                        <form class="navbar-search" action="javascript:;">
                            <div class="rel">
                                <h5 style="margin-top: 7px;margin-left: -15px;">
                                    {{strtoupper(Auth::user()->nama)}}
                                </h5>
                            </div>
                        </form>
                    </li>
                </ul>
                <!-- END TOP-LEFT TOOLBAR-->
                <!-- START TOP-RIGHT TOOLBAR-->
                <ul class="nav navbar-toolbar">
                    <li class="dropdown dropdown-user">
                        <a class="nav-link dropdown-toggle link" data-toggle="dropdown">
                            <!-- <img src="{{asset('photo/face.png')}}" /> -->
                            <i class="fa fa-cog rel"></i>&nbsp;
                            <span></span>Pengaturan</a>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-ubah-password"><i class="fa fa-cog"></i>Password</a>
                            <li class="dropdown-divider"></li>
                            <a class="dropdown-item" href="{{url('/logout')}}"><i class="fa fa-power-off"></i>Logout</a>
                        </ul>
                    </li>
                </ul>
                <!-- END TOP-RIGHT TOOLBAR-->
            </div>
        </header>
        <!-- END HEADER-->
        <div class="content-wrapper" style="margin-left: 0px !important;">
            <!-- START PAGE CONTENT-->
            <div class="page-content"> 
            	<!-- Main content -->
		        <?php 
				    loadHelper('format,url,akses'); 
				    date_default_timezone_set('Asia/Jakarta');
				?>
                <div class="ibox animated fadeInDown">
                    <div class="ibox-head">
                        <div class="ibox-title"><i class="fa fa-clone">&nbsp;&nbsp;</i>Data Kavling</div>
                    </div>
                    <div class="ibox-body">
                        <div class="form-group">
                            <div class="input-group">
                                <select class="form-control select2" name="project" id="project">
                                    <option value="0">[Pilihan]</option>
                                    @foreach($p as $d)
                                    <option value="{{$d->value}}">{{$d->text}}</option>
                                    @endforeach
                                </select>
                                <div class="input-group-btn">
                                    <button id="cari" class="btn btn-primary" type="button">Cari</button>
                                </div>
                            </div>
                        </div>
                        <table id="table" style="border-collapse: collapse; border-spacing: 0;"></table>
                    </div>
                </div>
            </div>
            <!-- END PAGE CONTENT-->
            <footer class="page-footer">
                <div class="font-13">© 2020 GRIYA KENCANA LESTARI | Designed by ADMINCAST </div>
            </footer>
        </div>
    </div>

    {{Html::bsModalOpenLg('submit-password', 'ubah-password', 'Ubah Password')}}
        {{Form::bsPassword('lama', 'Password Lama', '', 'true', '')}}
        {{Form::bsPassword('baru1', 'Password Baru', '', 'true', '')}}
        {{Form::bsPassword('baru2', 'Konfirmasi', '', 'true', '')}}
    {{Html::bsModalClose('Simpan')}}

    {{Html::bsModalOpen('', 'kavling', 'Detail Kavling')}}
        {{Form::bsRoText('no', 'No Kavling', '', '', '')}}
        {{Form::bsRoText('luas', 'Luas (m2)', '', '', '')}}
        {{Form::bsRoText('harga', 'Harga', '', '', '')}}
    {{Html::bsModalClose('')}}   
    <!-- core js-->

    <script src="{{asset('vendors/jquery/dist/jquery.min.js')}}"></script>
    <script src="{{asset('vendors/popper.js/dist/umd/popper.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/jquery.form.min.js')}}"></script>
    <script src="{{asset('js/app.min.js')}}"></script>
   
    <script src="{{asset('vendors/DataTables/datatables.min.js')}}"></script>
   
    <script type="text/javascript">
        $(document).ready(function(){
            $("#project").val(0).trigger('change');
            var id = $("#project").val();
            $("#table").DataTable({
                processing: true,
                serverSide: true,
                bLengthChange: false,
                bFilter: false,
                bInfo: false,
                bPaginate: false,
                order: [],
                aoColumnDefs: [
                    { orderable: false, targets: '_all' }
                ],
                ajax: "{{url_admin('sales')}}/datatable/"+id,
                columns: [
                    { data: 'data'}
                ]
            });
    
            {{Html::jsShowModal('ubah-password')}}
                $('.content').show();
            {{Html::jsCloseModal()}}
            {{Html::jsSubmitModal('ubah-password')}}

            {{Html::jsShowModal('kavling')}}
                var uuid = $(e.relatedTarget).data('uuid');
                $.ajax({
                    url:"{{url_admin('sales')}}/get-kavling/"+uuid,
                    success:function(data){
                        {{Html::jsValueForm('no','input','data.no_kavling')}}
                        {{Html::jsValueForm('luas','input','data.luas')}}
                        {{Html::jsValueForm('harga','money','data.total_harga')}}
                    },
                    error:function(data){
                        $("#modal-kavling").modal('hide');
                    }
                });
            {{Html::jsCloseModal()}}

            $("#cari").click(function(){
                var id = $("#project").val();
                $('#table').DataTable().ajax.url("{{url_admin('sales')}}/datatable/"+id).load();
            });

            $(".modal").on('hidden.bs.modal', function(){
                $(".form").trigger("reset");
            });
        });
    </script>
</body>

</html>