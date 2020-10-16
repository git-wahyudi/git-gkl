		<header class="header">
            <div class="page-brand" style="height: 80px;">
              
                    <div>
                    @if(Auth::user()->photo == null)
                        <img class="rounded-circle" src="{{asset('photo/face.png')}}" width="60px" />
                    @else
                        <img class="rounded-circle" src="{{asset('photo')}}/{{Auth::user()->photo}}" width="60px" />
                    @endif
                    </div>
                    <div class="admin-info ml-2" style="line-height: 17px;">
                        <div class="font-strong">{{ucfirst(Auth::user()->nama)}}</div><small>{{getRole()}}</small>
                    </div>
             
            </div>
            <div class="flexbox flex-1">
                <!-- START TOP-LEFT TOOLBAR-->
                <ul class="nav navbar-toolbar">
                    <li>
                        <a class="nav-link sidebar-toggler js-sidebar-toggler"><i class="ti-menu"></i></a>
                    </li>
                    <li class="d-none d-sm-block"> 
                        <form class="navbar-search" action="javascript:;">
                            <div class="rel">
                                <h5 style="margin-top: 7px;margin-left: -15px;">
                                    Griya Kencana Lestari
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
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-profile" data-id="{{encrypt(Auth::user()->id)}}"><i class="fa fa-user"></i>Profile</a>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal-ubah-password"><i class="fa fa-cog"></i>Password</a>
                            <li class="dropdown-divider"></li>
                            <a class="dropdown-item" href="{{url('/logout')}}"><i class="fa fa-power-off"></i>Logout</a>
                        </ul>
                    </li>
                </ul>
                <!-- END TOP-RIGHT TOOLBAR-->
            </div>
        </header>