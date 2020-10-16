<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <title>Griya Kencana Lestari | Login</title>
    <!-- GLOBAL MAINLY STYLES-->
    <link href="{{asset('vendors/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet" />
    <link href="{{asset('vendors/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" />
    <link href="{{asset('vendors/themify-icons/css/themify-icons.css')}}" rel="stylesheet" />
    <!-- THEME STYLES-->
    <link href="{{asset('css/main.css')}}" rel="stylesheet" />
    <!-- PAGE LEVEL STYLES-->
    <link href="{{asset('css/pages/auth-light.css')}}" rel="stylesheet" />
</head>

<body class="bg-silver-300">
    <div class="content">
        <form id="login-form" action="{{url('/submit-login')}}" method="post" style="border-radius: 10px; border: 1px solid;margin-top: 100px;">
          @csrf
        <div class="text-center mb-3 mt-3">
              <img src="{{asset('img/gkl.png')}}" width="110px" />
          </div>
            @if(Session::has('error'))
        <div class="form-group">
          <div class="col-xs-12">
                  <div class="alert alert-danger alert-dismissable" style="vertical-align: top;">
                      {{Session::get('error')}}
                  </div>
            </div>
          </div>
            @endif
            <div class="form-group">
                <div class="input-group-icon right">
                    <div class="input-icon"><i class="ti ti-user"></i></div>
                    <input class="form-control" type="text" name="key" placeholder="Username" autocomplete="off" autofocus="autofocus">
                </div>
            </div>
            <div class="form-group">
                <div class="input-group-icon right">
                    <div class="input-icon"><i class="ti ti-lock font-16"></i></div>
                    <input class="form-control" type="password" name="value" id="value" placeholder="Password">
                </div>
            </div>
            <div class="form-group d-flex justify-content-between">
                <label class="ui-checkbox ui-checkbox-info">
                    <input type="checkbox" onclick="myFunction()">
                    <span class="input-span"></span>Show password</label>
            </div>
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit" style="cursor: pointer;"><i class="fa fa-sign-in mr-1"></i> Login</button>
            </div>
            
        </form>
    </div>
    <!-- BEGIN PAGA BACKDROPS-->
    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    <!-- END PAGA BACKDROPS-->
    <!-- CORE PLUGINS -->
    <script src="{{asset('vendors/jquery/dist/jquery.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('vendors/popper.js/dist/umd/popper.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('vendors/bootstrap/dist/js/bootstrap.min.js')}}" type="text/javascript"></script>
  
    <!-- CORE SCRIPTS-->
    <script src="{{asset('js/app.js')}}" type="text/javascript"></script>
    <script type="text/javascript">
        function myFunction() {
          var x = document.getElementById("value");
          if (x.type === "password") {
            x.type = "text";
          } else {
            x.type = "password";
          }
        }
    </script>
</body>

</html>