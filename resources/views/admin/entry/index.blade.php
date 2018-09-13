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
                        <span class="caption-subject font-red sbold uppercase">报名信息</span>
                    </div>
                    <form style="float: right;" class="form-inline" method="get" action="{{route('entry.index')}}">
                        <div class="form-group">
                            <input type="text" class="form-control" name="contract_no" id="contract_no" placeholder="合同号">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="park_name" id="park_name" placeholder="园所名称">
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="apply_phone" id="apply_phone" placeholder="报名人手机号">
                        </div>
                        <div class="form-group">
                            <label for="">培训主题</label>
                            <select class="form-control" name="train_id" id="train_id">
                                <option value="">全部</option>
                                @foreach($trains as $train)
                                    <option value="{{ $train->id }}">{{ $train->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">报名方式</label>
                            <select class="form-control" name="from" id="from">
                                <option value="">全部</option>
                                <option value="1">线上</option>
                                <option value="2">线下</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">支付状态</label>
                            <select class="form-control" name="is_paid" id="is_paid">
                                <option value="">全部</option>
                                <option value="0">未支付</option>
                                <option value="1">已支付</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-default">搜索</button>
                        <button type="button" onclick="export_data()" class="btn btn-default">导出</button>
                        <a href="{{ route('entry.create') }}" class="btn green btn-outline">
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
                                <th > 园所合同号 </th>
                                <th > 园所名称 </th>
                                <th > 培训主题 </th>
                                {{--<th > 报名联系人 </th>--}}
                                <th > 手机号 </th>
                                <th > 报名人数 </th>
                                <th > 报名状态 </th>
                                <th > 报名方式 </th>
                                <th > 支付方式 </th>
                                <th > 支付费用 </th>
                                <th > 是否支付 </th>
                                <th > 支付时间 </th>
                                <th > 交易号 </th>
                                <th > 操作 </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lists as $list)
                                <tr id="trains_li_{{$list->id}}">
                                    <td>{{ $list->contract_no }}</td>
                                    <td>{{ $list->park_name }}</td>
                                    <td>{{ $list->get_train->title }}</td>
{{--                                    <td>{{ $list->apply_user_name }}</td>--}}
                                    <td>{{ $list->apply_phone }}</td>
                                    <td>{{ $list->apply_num }}</td>
                                    <td>
										@if($list->status == 1)
											已退款
										@elseif($list->status == 2)
											已取消
										@elseif($list->status == 3)
											审核中
										@elseif($list->status == 4)
											审核未通过
										@elseif($list->status == 6)
											已审核
										@endif
									</td>
                                    <td>
                                        @if($list->from ==1)
                                            线上
                                        @else
                                            线下
                                        @endif
                                    </td>
                                    <td>
                                        @if($list->payment ==1)
                                            微信
                                        @elseif($list->payment==2)
                                            支付宝
                                        @elseif($list->payment ==3)
                                            现金
                                        @else
                                            汇款
                                        @endif
                                    </td>
                                    <td>{{ $list->total_fee }}</td>
									<td>
                                        @if($list->is_paid ==1)
                                            已支付
                                        @else
                                            未支付
                                        @endif
                                    </td>
                                    <td>{{ $list->pay_time }}</td>
                                    <td>{{ $list->trade_no }}</td>
                                    <td>
                                        <a href="{{ route('students.index',['order_id'=>$list->id]) }}" class="btn btn-outline green btn-sm purple"><i class="fa fa-search-plus"></i>查看报名</a>
										@if($list->is_paid ==1 && $list->status !=1 && $list->from ==1)
                                        <a href="javascript:void(0);" onclick="show_refund({{ $list->id }})" class="btn btn-outline green btn-sm purple"><i class="fa fa-money"></i>退款</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{ $lists->appends($search)->links() }}
            </div>
        </div>
    </div>
	<div id="refund" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">退款</h4>
                </div>
                <div class="modal-body">
                    <div class="scroller" style="height:200px" data-always-visible="1" data-rail-visible1="1">
                        <div class="col-md-12">
                            <input type="hidden" id="refund_order_id" name="refund_order_id">
                            <div class="">
                                <p>
                                    <textarea class="form-control" style="margin: 0px 42px 0px 0px; height: 160px; width: 484px;" id="rremark" placeholder="退训原因"></textarea>
                                </p>
                            </div>
                            <p class="alert alert-danger" style="display: none" id="tag_error">
                                <strong>错误!</strong>&nbsp;&nbsp;<span id="post-error"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn dark btn-outline">关闭</button>
                    <button type="button" class="btn green" href="javascript:;" onclick="refund()">保存</button>
                </div>
            </div>
        </div>
    </div>
@endsection

{{--尾部前端资源--}}
@section('script')
    <script type="text/javascript">
        var is_paid = "{{ isset($search['is_paid'])?$search['is_paid']:'' }}";
        $("#is_paid").val(is_paid);
        var from = "{{ isset($search['from'])?$search['from']:'' }}";
        $("#from").val(from);
        var train_id = "{{ isset($search['train_id'])?$search['train_id']:'' }}";
        $("#train_id").val(train_id);
        //导出
        function export_data(){
            var contract_no   = $("#contract_no").val();
            var park_name     = $("#park_name").val();
            var apply_phone   = $("#apply_phone").val();
            var train_id      = $("#train_id").val();
            var s_train_id    = "{{ isset($search['train_id'])?$search['train_id']:'' }}";
            var from          = $("#from").val();
            var is_paid       = $("#is_paid").val();
            var trainId       = train_id?train_id:(s_train_id?s_train_id:'');

            window.location.href = "{{ url('admin/entry/export_data') }}?contract_no="+contract_no+"&park_name="+park_name+"&apply_phone="+apply_phone+"&train_id="+trainId+"&from="+from+"&is_paid="+is_paid;
        }
		function show_refund(id) {
            $("#refund_order_id").val(id);
            $("#refund").modal();
        }
        function refund() {
            var refund_order_id = $("#refund_order_id").val();
            var rremark = $("#rremark").val();
            $.ajax({
                url:"{{ url('admin/entry/refund') }}",
                type:"POST",
                data:{
                    id:refund_order_id,
                    remark:rremark,
                    '_token': "{{ csrf_token() }}"
                },
                success:function (e) {
                    if(e.code=='200'){
                        $('#refund').modal('hide');
                        swal("OK!", e.msg, "success");
                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    }else{
                        $('#refund').modal('hide');
                        swal("OOPS!", e.msg, "error");
                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
                    }
                },
				error:function(e){
					if(e.status){
						$('#refund').modal('hide');
                        swal("OOPS!", '没有退款权限', "error");
                        setTimeout(function () {
                            window.location.reload();
                        }, 2000);
					}
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

