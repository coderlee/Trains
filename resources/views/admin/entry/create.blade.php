@extends('admin.layouts.main')

{{--顶部前端资源--}}
@section('styles')
    <link href="{{ asset('vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css') }}" rel="stylesheet" type="text/css" />
@endsection

{{--页面内容--}}
@section('content')
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="portlet light portlet-fit portlet-form bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class=" icon-layers font-green"></i>
                        <span class="caption-subject font-green sbold uppercase">新增学员</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <!-- BEGIN FORM-->
                    <form action="{{ route('entry.store') }}" class="form-horizontal" id="entryForm" method="post">
                        {{ csrf_field() }}
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="train_id">培训主题</label>
                                <div class="col-md-9">
                                    <select class="checkbox" name="train_id" id="train_id">
                                        <option value="">选择培训</option>
                                        @foreach($trains as $train)
                                            <option value="{{ $train->id }}">{{ $train->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group form-md-line-input">
                                <label class="col-md-3 control-label" for="contract_no">园所合同号</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" required name="contract_no" id="contract_no" placeholder="" value="">
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">{{ $errors->has('contract_no') ? $errors->first('contract_no') : '园所合同号' }}</span>
                                </div>
                            </div>
                            <div class="form-group form-md-line-input">
                                <label class="col-md-3 control-label" for="park_name">园所名称</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" readonly placeholder="" name="park_name" id="park_name"  value="">
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">{{ $errors->has('park_name') ? $errors->first('park_name') : '园所名称' }}</span>
                                </div>
                            </div>
                            <div class="form-group form-md-line-input">
                                <label class="col-md-3 control-label" for="student_name">学员姓名</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" required placeholder="" name="student_name" id="student_name" value="">
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">{{ $errors->has('student_name') ? $errors->first('student_name') : '学员姓名' }}</span>
                                </div>
                            </div>
                            <div class="form-group form-md-line-input">
                                <label class="col-md-3 control-label" for="student_phone">学员手机号</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" required placeholder="" name="student_phone" id="student_phone" value="">
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">{{ $errors->has('student_phone') ? $errors->first('student_phone') : '学员手机号' }}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="student_sex">学员性别</label>
                                <div class="col-md-9">
                                    <label class="radio-inline">
                                        <input type="radio" name="student_sex" value="1"> 男
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" checked name="student_sex" value="2">女
                                    </label>
                                </div>
                            </div>
                            <div class="form-group form-md-line-input">
                                <label class="col-md-3 control-label" for="student_position">学员岗位</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" placeholder="" name="student_position" id="student_position" value="">
                                    <div class="form-control-focus"> </div>
                                    <span class="help-block">{{ $errors->has('student_position') ? $errors->first('student_position') : '学员岗位' }}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="payment">缴费方式</label>
                                <div class="col-md-9">
                                    <label class="radio-inline">
                                        <input type="radio" checked name="payment" value="1"> 微信
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="payment" value="2">支付宝
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="payment" value="3">现金
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="payment" value="4">汇款
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="student_position">费用(元)</label>
                                <div class="col-md-4">
                                    <input type="number" class="form-control " placeholder="" name="total_fee" id="total_fee" value="0">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-3 control-label" for="pay_time">缴费日期</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="" name="pay_time" id="pay_time" value="">
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <input type="hidden" name="student_status" id="student_status" value="1">
                                    <input type="submit" class="btn green" value="保存">
                                    <input type="button" onclick="sign()" class="btn red" value="签到">
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- END FORM-->
                </div>
            </div>
        </div>
        <div class="col-md-3"></div>
    </div>
@endsection

{{--尾部前端资源--}}
@section('script')
    <script src="{{asset('vendor/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('assets/admin/layouts/scripts/sweetalert/sweetalert-ajax-delete.js')}}" type="text/javascript"></script>
    <script>
        $(function () {
            SweetAlert.init();
            $.fn.datetimepicker.dates['zh-CN'] = {
                days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
                daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
                daysMin:  ["日", "一", "二", "三", "四", "五", "六", "日"],
                months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
                today: "今日",
                suffix: [],
                meridiem: []
            };
            $("#pay_time").datetimepicker({
                autoclose: true,
                todayHighlight: true,
                language:"zh-CN",
                format:"yyyy-mm-dd hh:ii:ss"
            });
            $("#contract_no").blur(function () {
                var contract_no = $("#contract_no").val();
                if(contract_no){
                    $.ajax({
                        url:"{{ url('admin/entry/check_nursery') }}",
                        type:'POST',
                        data:{
                            contract_no:contract_no,
                            '_token': "{{ csrf_token() }}"
                        },
                        success:function (e) {
                            if(e.schName){
                                $("#park_name").val(e.schName);
                            }else{
                                swal("OOPS!", "未匹配到园所！", "error");
                            }
                        }
                    })
                }
            })
        })
        function sign() {
            $("#student_status").val(3);
            $("#entryForm").submit();
            return true;
        }
    </script>
@endsection