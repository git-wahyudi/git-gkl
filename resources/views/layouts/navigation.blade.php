		<?php
            $path = Request::segment(2);
            $menu_session = Session::get('menu_session');
            $menu_session = json_decode($menu_session);
        ?>
        <nav class="page-sidebar" id="sidebar">
            <div id="sidebar-collapse">
                <ul class="side-menu metismenu">
                    <li class="@if($path == 'dashboard') active @endif()">
                        <a href="{{url('/admin')}}"><i class="sidebar-item-icon fa fa-th-large"></i>
                            <span class="nav-label">Dashboard</span>
                        </a>
                    </li>
                    @foreach($menu_session as $mnu_induk)
                    <li id="mnu_p_{{$mnu_induk->id_menu}}" class="@if($mnu_induk->id_menu == active($path)) active @endif()">
                        <a href="javascript:;"><i class="sidebar-item-icon {{$mnu_induk->icon}}">   
                            </i><span class="nav-label">{{$mnu_induk->nama_menu}}</span><i class="fa fa-angle-left arrow"></i>
                        </a>
                        <ul class="nav-2-level collapse">
                            @foreach($mnu_induk->child as $mnu_child)
                            <li id="mnu_c_{{$mnu_child->id_menu}}">
                                <a href="{{url_admin($mnu_child->url)}}" class="@if($path==$mnu_child->url) active @endif">{{$mnu_child->nama_menu}}</a>
                            </li>
                            @endforeach
                        </ul>
                    </li>
                    @endforeach
                </ul>
            </div>
        </nav>
                    