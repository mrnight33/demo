<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>用户登录 | BigPang</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="/plugins/iCheck/square/blue.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="/libs/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>龙湖</b> <b>源著</b>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg"><b>登录</b></p>

        <form action="{{ url('/admin/login') }}" method="post">
            {!! csrf_field() !!}
            <div class="form-group has-feedback">
                <input type="email" class="form-control" placeholder="登录邮箱名"  name="email" value="" required>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                @if(isset($email))
                    <span style="color: #ff0000;" class="error" >{{$email[0]}}</span>
                @endif
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control"  name="pwd" placeholder="密码" required>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                @if(isset($pwd))
                <span style="color: #ff0000;" class="error" >{{$pwd[0]}}</span>
                @endif
            </div>
            <div class="form-group has-feedback">
                <div class="row">
                    <div class="col-xs-7 ">
                        <input type="text" class="form-control"  name="code" placeholder="验证码" required>
                    </div>
                    <div class="col-xs-5">
                        <img src="{{ url('captcha/default') }}" class="img-responsive" onclick="this.src='{{ url('captcha/default') }}?r='+Math.random();"  alt="">
                    </div>
                </div>
                @if(isset($error)&&($error===95||$error==96||$error==1))
                    <span style="color: #ff0000;" class="error" >{{$desc}}</span>
                @endif
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox">&nbsp;&nbsp;记&nbsp;住&nbsp;
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">登录</button>
                </div>
            </div>
        </form>
        <a href="#">我忘了密码</a>
    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.2.0 -->
<script src="/plugins/jQuery/jQuery-2.2.0.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="/bootstrap/js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="/plugins/iCheck/icheck.min.js"></script>
<script>
    $(function () {
        $('input').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' // optional
        });
    });
</script>
</body>
</html>
