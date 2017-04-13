<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>地图标注</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    {{--<link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">--}}
    {{--<!-- Font Awesome -->--}}
    {{--<link rel="stylesheet" href="/libs/font-awesome/4.5.0/css/font-awesome.min.css">--}}
    {{--<!-- Ionicons -->--}}
    {{--<link rel="stylesheet" href="/libs/ionicons/2.0.1/css/ionicons.min.css">--}}
    {{--<!-- Theme style -->--}}
    {{--<link rel="stylesheet" href="/dist/css/AdminLTE.min.css">--}}
    {{--<!-- iCheck -->--}}
    {{--<link rel="stylesheet" href="/plugins/iCheck/square/blue.css">--}}

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <!--<script src="/libs/html5shiv/3.7.3/html5shiv.min.js"></script>-->
    <!--<script src="/libs/respond/1.4.2/respond.min.js"></script>-->
    <style type="text/css">
        body,html,#container{
            height: 100%;
            margin: 0px;
        }
    </style>
    <![endif]-->
</head>
<body>
<div id="container" tabindex="0"></div>
<script type="text/javascript" src="http://webapi.amap.com/maps?v=1.3&key=0b1c1b8cb43caa09a2428167e430abda"></script>
<script type="text/javascript">
    var map = new AMap.Map('container',{
        resizeEnable: true,
        zoom: 17,
        center: [{{$longitude}}, {{$latitude}}]
    });
</script>
</body>
</html>
