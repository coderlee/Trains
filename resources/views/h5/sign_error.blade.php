<!DOCTYPE html>
<html lang="zh">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta content="telephone=no" name="format-detection" />
    <script src="{{ asset('assets/h5/js/common.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/h5/css/normalize.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/h5/css/baseStyle.css') }}" />
    <style>
        a {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 1rem;
            line-height: 1rem;
            text-align: center;
            text-decoration: none;
            color: #fff;
            background-color: #09bb07;
        }
		.rwm_s{
    display: block;
    width: 3rem;
    height: 3rem;
    margin: 0 auto;
		}
		.tetyx_al{
			margin-top:10px;
			
		}
    </style>
    <script src="{{ asset('assets/h5/js/restRem.js') }}"></script>
    <title>报名失败提示</title>
</head>

<body>
<!-- 头部开始 -->
<header>
    <img src="{{ asset('assets/h5/imgs/error.png') }}" alt="签到成功提示图片">
    <h2>签到失败</h2>
    <div class="hr"></div>
</header>
<!-- 头部结束 -->
<!-- 内容开始 -->
<main>
    <p>手机号错误或未报名缴费</p>
    <p>签到失败，请联系签到老师进行签到</p>
	<p>
		<img class="rwm_s" src="{{ asset('assets/h5/imgs/gh_335fc93449af_430.jpg') }}">
	</p>
	<p class="tetyx_al">
		 长安以上二维码进入小程序签到报名
	</p>
</main>
<!-- 内容结束 -->
<!-- 底部悬浮框开始 -->

</body>

</html>