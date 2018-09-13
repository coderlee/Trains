@extends('admin.layouts.main')

{{--顶部前端资源--}}
@section('styles')
    {{--ajax使用--}}
    <link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

{{--页面内容--}}
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light portlet-fit portlet-datatable bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-settings font-red"></i>
                        <span class="caption-subject font-red sbold uppercase">培训列表</span>
                    </div>
                    <form style="float: right;" class="form-inline" method="get" action="{{route('trains.index')}}">
                        <div class="form-group">
                            <input type="text" class="form-control" name="title" id="title" placeholder="培训主题名称...">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName2">报名有效期</label>
                            <input type="text" name="apply_start" class="form-control" id="datetimepicker1">至
                            <input type="text" name="apply_end" class="form-control" id="datetimepicker2">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputName2">培训时间</label>
                            <input type="text" name="train_start" class="form-control" id="datetimepicker3">至
                            <input type="text" name="train_end" class="form-control" id="datetimepicker4">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="recorder" id="recorder" placeholder="创建人...">
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="status" id="status">
                                <option value="">全部</option>
                                <option value="1">待发布</option>
                                <option value="2">已发布</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-default">搜索</button>
                        <a href="{{ route('trains.create') }}" class="btn green btn-outline">
                            <i class="fa fa-edit"></i>
                            新增
                        </a>
                    </form>
                </div>
                <div class="portlet-body">
                    <div class="table-container">
                        <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                            <thead>
                            <tr role="row" class="heading">
                                <th > 培训主题名称 </th>
                                <th > 预计报名人数 </th>
                                <th > 已报名人数 </th>
                                <th > 报名有效期 </th>
                                <th > 培训时间 </th>
                                <th > 创建人 </th>
                                <th > 状态 </th>
                                <th > 操作 </th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($lists as $list)
                                    <tr id="trains_li_{{$list->id}}">
                                        <td>{{ $list->title }}</td>
                                        <td>{{ $list->pre_num }}</td>
                                        <td>{{ $list->sale_num }}</td>
                                        <td>{{ $list->apply_start }}至{{ $list->apply_end }}</td>
                                        <td>{{ $list->train_start }}至{{ $list->train_end }}</td>
                                        <td>{{ $list->admin_user->name }}</td>
                                        <td>
                                            @if($list->status ==1)
                                                待发布
                                            @else
                                                已发布
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('trains.edit',$list->id) }}" class="btn btn-outline green btn-sm purple"><i class="fa fa-edit"></i>编辑</a>
                                            @if($list->status==1)
                                                <a href="javascript:void(0);" onclick="changeStatus({{ $list->id }})" class="btn btn-outline green btn-sm purple"><i class="fa fa-search-plus"></i>发布</a>
                                            @else
                                                <a href="{{ route('students.index',['train_id'=>$list->id]) }}" class="btn btn-outline green btn-sm purple"><i class="fa fa-search-plus"></i>查看报名</a>
                                                <a href="javascript:;" onclick="signCode({{ $list->id }})" class="btn blue mt-ladda-btn ladda-button btn-circle btn-outline">
                                                    <i class="fa fa fa-eye"></i>签到报名码
                                                </a>
                                                <a href="{{ url('admin/trains/download',$list->id) }}" class="btn blue mt-ladda-btn ladda-button btn-circle btn-outline">
                                                    <i class="fa fa fa-download"></i>签到报名码
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{ $lists->links() }}
            </div>
        </div>
    </div>

    {{-- 签到报名码 --}}
    <div id="qrcode" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">签到报名码</h4>
                </div>
                <div class="modal-body">
                    <div class="scroller" style="height:200px" data-always-visible="1" data-rail-visible1="1">
                        <div class="col-md-12">
                            <img id="qrcode_img" src="" alt="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">关闭</button>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--尾部前端资源--}}
@section('script')
    <script type="text/javascript">
        $(function () {
            !function(a){a.fn.datepicker.dates["zh-CN"]={days:["星期日","星期一","星期二","星期三","星期四","星期五","星期六"],daysShort:["周日","周一","周二","周三","周四","周五","周六"],daysMin:["日","一","二","三","四","五","六"],months:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],monthsShort:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"],today:"今日",clear:"清除",format:"yyyy年mm月dd日",titleFormat:"yyyy年mm月",weekStart:1}}(jQuery);
            $("#datetimepicker1,#datetimepicker2,#datetimepicker3,#datetimepicker4").datepicker({
                autoclose: true,
                todayHighlight: true,
                language:"zh-CN",
                format:"yyyy-mm-dd"
            });

            SweetAlert.init();
        })
        function changeStatus(id) {
            swal({
                title: "确定发布吗",
                text: "",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                showCancelButton:true,
                confirmButtonText: '确认',
                cancelButtonText: '取消',
            },function(isConfirm){
                if (isConfirm) {
                    $.ajax({
                        type: "POST",
                        url:"{{ url('admin/trains/change_status') }}",
                        data:{
                            id:id,
                            '_token': "{{ csrf_token() }}"
                        },
                        dataType: "json",
                        success:function(e){
                            if(e.code =='200'){
                                swal("Good job!", "发布成功！", "success");
                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            }
                        }
                    })
                }else{

                }
            })
        }
        function signCode(id) {
            $.ajax({
                type:"POST",
                url:"{{ url('admin/trains/qrcode') }}",
                data:{
                    'train_id':id,
                    '_token': "{{ csrf_token() }}"
                },
                dataType: "json",
                success:function (e) {
                    $("#qrcode_img").attr('src','data:image/png;base64, '+e.code);
                    $("#qrcode").modal();
                }
            })
        }
    </script>
    <script src="{{asset('assets/admin/layouts/scripts/datatable.js')}}" type="text/javascript"></script>
    <script src="{{asset('vendor/datatables/datatables.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('vendor/datatables/plugins/bootstrap/datatables.bootstrap.js')}}" type="text/javascript"></script>
    {{--ajax使用--}}
    <script src="{{asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
    {{--sweetalert弹窗--}}
    <script src="{{asset('assets/admin/layouts/scripts/sweetalert/sweetalert-ajax-delete.js')}}" type="text/javascript"></script>
@endsection

