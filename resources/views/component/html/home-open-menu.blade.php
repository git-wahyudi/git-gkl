				<?php 
				    loadHelper('format,url,akses'); 
				    date_default_timezone_set('Asia/Jakarta');
				?>
                <div class="ibox animated fadeInDown">
                    <div class="ibox-head">
                        <div class="ibox-title"><i class="fa fa-clone">&nbsp;&nbsp;</i>{{$title}}</div>
                    </div>
                    <div class="ibox-body">
                        <div class="table-responsive">
                       
                        <div style="margin-bottom: -28px;">
                            <a type="button" class="btn btn-success text-white" data-toggle="modal" data-target="#modal-{{$link}}" style="z-index: 10;"><i class="fa fa-plus-square-o mr-2"></i>{!!$text!!}</a>
                        </div>
                        