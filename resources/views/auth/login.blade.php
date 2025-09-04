<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>Login | AdminBSB</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap Core Css -->
    <link href="{{ asset('plugins/bootstrap/css/bootstrap.css') }}" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="{{ asset('plugins/node-waves/waves.css') }}" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="{{ asset('plugins/animate-css/animate.css') }}" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />
</head>

<body class="login-page" style="background: linear-gradient(to right, #6a11cb, #2575fc);">
    <div class="login-box animated fadeInDown">
        <div class="logo text-center" style="margin-bottom: 20px;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 120px; margin-bottom:0;">
            <h3 style="color:white;"><b>Admin</b>BSB</h3>
            <small style="color:white;">Material Design Bootstrap</small>
        </div>
        <div class="card">
            <div class="body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="msg">Sign in to start your session</div>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Email" required
                                autofocus>
                        </div>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                    </div>

                    <button class="btn btn-block bg-deep-purple waves-effect" type="submit">SIGN IN</button>

                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-6">
                            <a href="javascript:void(0);" style="color:#2575fc;">Register Now!</a>
                        </div>
                        <div class="col-xs-6 align-right">
                            <a href="javascript:void(0);" style="color:#2575fc;">Forgot Password?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


</html>
