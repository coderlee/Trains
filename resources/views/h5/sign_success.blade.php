<!DOCTYPE html>
<html lang="zh">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta content="telephone=no" name="format-detection" />
    <script src="{{ asset('assets/h5/js/common.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/h5/css/normalize.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/h5/css/baseStyle.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/h5/css/Success.css') }}" />
    <script src="{{ asset('assets/h5/js/restRem.js') }}"></script>
    <title>培训签到</title>
</head>
<body>
<!-- 头部开始 -->
<header>
    <img src="{{ asset('assets/h5/imgs/success.png') }}" alt="签到成功提示图片">
    <h2>签到成功</h2>
    <span>请勿关闭此页面，出示给签到老师即可</span>
    <div class="hr"></div>
</header>
<!-- 头部结束 -->
<!-- 内容开始 -->
<main>
    <h2>学员信息</h2>
    <div class="userInfo">
        <div class="infoList clearfloat">
            <div class="fl">所报课程：</div>
            <div class="fr course"></div>
        </div>
        <div class="infoList clearfloat">
            <div class="fl">园所合同号：</div>
            <div class="fr contract"></div>
        </div>
        <div class="infoList clearfloat">
            <div class="fl">园所名称：</div>
            <div class="fr address"></div>
        </div>
        <div class="infoList clearfloat">
            <div class="fl">学员姓名：</div>
            <div class="fr userName"></div>
        </div>
        <div class="infoList clearfloat">
            <div class="fl">学员手机号：</div>
            <div class="fr userPhone"></div>
        </div>
        <div class="infoList clearfloat">
            <div class="fl">学员性别：</div>
            <div class="fr userSex"></div>
        </div>
        <div class="infoList clearfloat">
            <div class="fl">学员岗位：</div>
            <div class="fr userWork"></div>
        </div>
    </div>
</main>
<!-- 内容结束 -->
<script src="{{ asset('assets/h5/js/jquery3.3.1.js') }}"></script>
<script>
    (function () {
        var info =sessionStorage.getItem("info")
        var userInfo = JSON.parse(info);
        $(".course").text(userInfo.get_order.get_train.title);
        $(".contract").text(userInfo.get_nursery_user.contract_no);
        $(".address").text(userInfo.get_order.get_train.train_adress);
        $(".userName").text(userInfo.get_nursery_user.student_name);
        $(".userPhone").text(userInfo.get_nursery_user.student_phone);
		var sex = userInfo.get_nursery_user.student_sex =='1'?'男':'女';
        $(".userSex").text(sex);
        $(".userWork").text(userInfo.get_nursery_user.student_position);
    })()
</script>
</body>

</html>