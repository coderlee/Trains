<!DOCTYPE html>
<html lang="zh">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta content="telephone=no" name="format-detection" />
    <script src="{{ asset('assets/h5/js/jquery3.3.1.js') }}"></script>
    <script src="{{ asset('assets/h5/js/common.js') }}"></script>
    <script src="{{ asset('assets/h5/js/restRem.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/h5/css/normalize.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/h5/css/baseStyle.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/h5/css/login.css') }}" />
    <title>培训签到</title>
</head>

<body>
<!-- 头部开始 -->
<header></header>
<!-- 头部结束 -->
<!-- 内容开始 -->
<main>
    <form id="siginForm" type="post">
        <div class="inpuBox">
            <input type="hidden" id="train_id" name="train_id" value="{{ $train_id }}" />
            <div class="phoneNum"><input type="tel" name="student_phone" maxlength="11" placeholder="请输入手机号">  <span id="testBtn">获取验证码</span></div>
            <div class="testNum"><input class="my_code" type="number" name="code" maxlength="6" placeholder="请输入验证码"></div>
        </div>
        <span id="sigin">签到</span>
    </form>
</main>
<!-- 内容结束 -->
<script>
    (function(){
        $("#testBtn").on("click",function(e){
            e.stopPropagation();
            e.preventDefault();
            var testPhone = $(".phoneNum input").val();
            let re = /^1\d{10}$/;
            if(!re.test(testPhone))
            {
                alert("手机号输入有误");
                return false;
            }
            $(this).attr("disabled",true);
            $.ajax({
                url:"{{ url('h5/send_code')}}",
                type:"get",
                data:{
                    "student_phone":testPhone
                },
                dataType:"json",
                success:function(res){
                    if(res.code=="200"||res.code=="1008"){
                        var time =60;
                        if(res.code=="1008"){
                            time=res.data.exipre;
                        }
                        var timer = setInterval(function () {
                            if(time == 0){
                                $("#testBtn").removeAttr("disabled");
                                $("#testBtn").text("获取验证码");
                                clearInterval(timer);
                            }else {
                                $("#testBtn").text(time);
                                time--;
                            }
                        },1000);
                    }
                },
                error(res){

                }
            });
        });
        $("#sigin").click(function(e){
            var _this=$(this);
            if($(this).hasClass("isAjaxAgain")){
                return false;
            }
            $(this).addClass("isAjaxAgain");
            e.preventDefault();
            var testPhone = $(".phoneNum input").val();
            let re = /^1\d{10}$/;
            if(!re.test(testPhone))
            {
                alert("手机号输入有误");
				$(_this).removeClass("isAjaxAgain")
                return false;
            }
            if(!$(".my_code").val()){
                alert("请输入验证码!");
				$(_this).removeClass("isAjaxAgain")
                return false;
            };
            var sigin = $("#siginForm").serialize();
            $.ajax({
                url:"{{ url('h5/check_student') }}",
                type:"post",
                data:sigin,
                dataType:"json",
                success:function(res){
                    if(res.code=="200"){
                        document.getElementById("siginForm").reset();
						console.log(res)
                        sessionStorage.setItem("info",JSON.stringify(res.data));
                       window.location.href="{{ url('h5/sign_success') }}";
                    }else{
                        alert(res.msg);
                    }
                },
                error(res){
                    window.location.href="{{ url('h5/sign_error') }}?train_id="+$("#train_id").val()+"&student_phone="+testPhone;
                },
                complete:function(){
                    $(_this).removeClass("isAjaxAgain");
                }
            });
        });
    })()
</script>
</body>

</html>