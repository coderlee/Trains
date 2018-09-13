@extends('admin.layouts.main')

{{--顶部前端资源--}}
@section('styles')
    <link href="{{ asset('vendor/bootstrap-fileinput/bootstrap-fileinput.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/select2/css/select2-bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/bootstrap-select/css/bootstrap-select.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('vendor/bootstrap-datepicker/css/bootstrap-datepicker3.min.css') }}" rel="stylesheet" type="text/css" />
    {!! editor_css() !!}
@endsection

{{--页面内容--}}
@section('content')
    <div class="row">
        <div class="col-md-10 ">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption font-red-sunglo">
                        <i class="icon-settings font-red-sunglo"></i>
                        <span class="caption-subject bold uppercase"> 新增培训 </span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <form role="form" enctype="multipart/form-data" method="post" action="@if($is_edit){{ route('trains.update', [$train->id]) }}@else{{ route('trains.store') }}@endif">
                        {{ csrf_field() }}
                        @if($is_edit)
                            <input type="hidden" name="_method" value="PUT">
                        @endif
                        <div class="form-body">
                            <div class="form-group @if($errors->has('title')) has-error @endif">
                                <label><span style="color:red">*</span>培训主题名称</label>
                                <div class="input-icon">
                                    <i class="fa fa-keyboard-o font-green"></i>
                                    <input type="text" class="form-control" name="title" placeholder="培训主题名称"
                                           value="{{ $is_edit ? $train->title : old('title') }}"
                                    >
                                    @if($errors->has('title'))
                                        <span class="help-block">{{ $errors->first('title') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group last">
                                <label class="control-label"><span style="color:red">*</span>banner图(建议尺寸...)</label>
                                <div class="">
                                    <div class="fileinput fileinput-new" data-provides="fileinput">
                                        <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                            <img src="@if($is_edit){{ $train->banner ?url($train->banner): asset('assets/admin/img/no_image.png') }}@else{{ asset('assets/admin/img/no_image.png') }}@endif" alt="" /> </div>
                                        <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                                        <div>
                                            <span class="btn default btn-file">
                                                <span class="fileinput-new"> 选择图片 </span>
                                                <span class="fileinput-exists"> 更换 </span>
                                                <input type="file" name="banner">
                                            </span>
                                            <a href="javascript:;" class="btn red fileinput-exists" data-dismiss="fileinput"> 删除 </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><span style="color:red">*</span>报名有效期</label>
                                <div class="input-group">
                                    <input type="text" class="input-sm" name="apply_start" id="apply_start" value="{{ $is_edit ? $train->apply_start:'' }}">至
                                    <input type="text" class="input-sm" name="apply_end" id="apply_end" value="{{ $is_edit ? $train->apply_end:'' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label><span style="color:red">*</span>预计报名人数</label>
                                <div class="input-group">
                                    <input type="number" class="input-sm" name="pre_num" id="pre_num" value="{{ $is_edit ? $train->pre_num:'' }}">人
                                </div>
                            </div>
                            @if($is_edit)
                            <div class="form-group">
                                <label>虚拟报名人数</label>
                                <div class="input-group">
                                    <input type="number" class="input-sm" name="jia_sale_num" id="jia_sale_num" value="{{ $is_edit ? $train->jia_sale_num:'0' }}">人
                                </div>
                            </div>
                            @endif
                            <div class="form-group">
                                <label><span style="color:red">*</span>培训地点</label>
                                <div class="input">
                                    <input type="text" class="form-control" name="train_adress" id="train_adress" {{ $is_edit?($train->status ==2?'readonly':''):'' }} value="{{ $is_edit ? $train->train_adress:'' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label><span style="color:red">*</span>培训时间</label>
                                <div class="input-group">
                                    <input type="text" class="input-sm" name="train_start" id="train_start" value="{{ $is_edit ? $train->train_start:'' }}">至
                                    <input type="text" class="input-sm" name="train_end" id="train_end" value="{{ $is_edit ? $train->train_end:'' }}">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for=""><span style="color:red">*</span>是否付费</label>
                                <div class="radio">
                                    <label class="radio-inline">
                                        <input type="radio" onclick="change_free(0)" checked name="is_free" value="0"> 免费
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" onclick="change_free(1)" name="is_free" value="1">付费
                                    </label>
                                </div>
                            </div>
                            <div class="form-group no_free" style="display: none;">
                                <label for=""><span style="color:red">*</span>收费方式</label>
                                <div class="input-group" style="margin:10px 0px;" id="">
                                    <input type="radio" name="charge_way" value="1">
                                    <input type="text" class="input-sm" name="attr1_name_1" value="单人价">
                                    <input type="text" class="input-sm" name="attr1_price_1" {{ $is_edit?($train->status ==2?'readonly':''):'' }} onkeyup="value=value.replace(/[^\d.]/g,'')" value="">元&nbsp;
                                    <input type="text" class="input-sm" name="attr2_name_1" value="团报价">
                                    <input type="text" class="input-sm" name="attr2_price_1" {{ $is_edit?($train->status ==2?'readonly':''):'' }} onkeyup="value=value.replace(/[^\d.]/g,'')" value="">元&nbsp;
                                    <input type="number" name="min_num" value="" />人以上
                                </div>
                                <div class="input-group" style="margin:10px 0px;" id="">
                                    <input type="radio" name="charge_way" value="2">
                                    <input type="text" class="input-sm" name="attr1_name_2" value="优惠价">
                                    <input type="text" class="input-sm" name="attr1_price_2" {{ $is_edit?($train->status ==2?'readonly':''):'' }} onkeyup="value=value.replace(/[^\d.]/g,'')" value="">元&nbsp;
                                    <input type="text" class="input-sm" name="attr2_name_2" value="原价">
                                    <input type="text" class="input-sm" name="attr2_price_2" {{ $is_edit?($train->status ==2?'readonly':''):'' }} onkeyup="value=value.replace(/[^\d.]/g,'')" value="">元&nbsp;
                                    <input type="text" class="input-sm" name="attr3_name_2" value="运委会价">
                                    <input type="text" class="input-sm" name="attr3_price_2" {{ $is_edit?($train->status ==2?'readonly':''):'' }} onkeyup="value=value.replace(/[^\d.]/g,'')" value="">元&nbsp;
                                </div>
                                <div class="input-group" style="margin:10px 0px;" id="">
                                    <input type="radio" name="charge_way" value="3">
                                    <input type="text" class="input-sm" name="attr1_name_3" value="已购买教具">
                                    <input type="text" class="input-sm" name="attr1_price_3" {{ $is_edit?($train->status ==2?'readonly':''):'' }} onkeyup="value=value.replace(/[^\d.]/g,'')" value="">元&nbsp;
                                    <input type="text" class="input-sm" name="attr2_name_3" value="未购买教具">
                                    <input type="text" class="input-sm" name="attr2_price_3" {{ $is_edit?($train->status ==2?'readonly':''):'' }} onkeyup="value=value.replace(/[^\d.]/g,'')" value="">元&nbsp;
                                    <input type="text" class="input-sm" name="attr3_name_3" value="">
                                    <input type="text" class="input-sm" name="attr3_price_3" {{ $is_edit?($train->status ==2?'readonly':''):'' }} onkeyup="value=value.replace(/[^\d.]/g,'')" value="">元&nbsp;
                                </div>
                            </div>
                            <div class="form-group no_free" style="display: none;">
                                <label>收费单位</label>
                                <select class="input-group" name="unit" id="unit">
                                    <option value="1" selected >人</option>
                                    <option value="2" >园所</option>
                                </select>
                            </div>
                            <div class="form-group max_nursery" style="display: none;">
                                <label>每园限制人数</label>
                                <div class="input-group">
                                    <input type="number" class="input-sm" name="max_nursery_num" id="max_nursery_num" value="{{ $is_edit ? $train->get_charge->max_nursery_num :'0' }}">人
                                </div>
                            </div>
                            <div class="form-group">
                                <label>排序</label>
                                <div class="input-group">
                                    <input type="number" class="input-sm" name="sort" id="sort" value="{{ $is_edit ? $train->sort:'0' }}">
                                </div>
                            </div>
							<div class="form-group">
                                <label for="">身份证资料是否上传</label>
                                <div class="radio">
                                    <label class="radio-inline">
                                        <input type="radio" checked name="is_card" value="0"> 否
                                    </label>
                                    <label class="radio-inline">
                                       <input type="radio" name="is_card" value="1">是
                                   </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">健康证资料是否上传</label>
                                <div class="radio">
                                    <label class="radio-inline">
                                        <input type="radio" checked name="is_health" value="0"> 否
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="is_health" value="1">是
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">劳动合同资料是否上传</label>
                                <div class="radio">
                                    <label class="radio-inline">
                                        <input type="radio" checked name="is_labor" value="0"> 否
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="is_labor" value="1">是
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="">培训协议是否上传</label>
                                <div class="radio">
                                    <label class="radio-inline">
                                            <input type="radio" checked name="is_learnership" value="0"> 否
                                        </label>
                                    <label class="radio-inline">
                                            <input type="radio" name="is_learnership" value="1">是
                                        </label>
                                </div>
                            </div>
							<div class="form-group">
                                <label><span style="color:red">*</span>声明</label>
                                <div class="input-group">
									<textarea name="shengming" rows="3" cols="100" placeholder="声明" >{{ $is_edit ? $train->shengming:'' }}</textarea>
                                </div>
                            </div>
                            <div class="form-group @if($errors->has('editormd-html-code')) has-error @endif">
                                <label><span style="color:red">*</span>培训详情</label>
                                <div id="editormd">
                                    <textarea style="" id="article_content">{{ $is_edit ? $train->desc_md : '培训详情' }}</textarea>
                                </div>
                                @if($errors->has('editormd-html-code'))
                                    <span class="help-block">{{ $errors->first('editormd-html-code') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn blue">保存</button>
                            <button type="button" onclick="javascript:history.go(-1);" class="btn default">取消</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>
@endsection

{{--尾部前端资源--}}
@section('script')
    <script src="{{ asset('vendor/bootstrap-fileinput/bootstrap-fileinput.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/layouts/scripts/components-select2.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/bootstrap-select/js/bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/admin/layouts/scripts/components-bootstrap-select.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/InlineAttachment/src/inline-attachment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('vendor/InlineAttachment/src/jquery.inline-attachment.js') }}" type="text/javascript"></script>
    <script src="{{asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}" type="text/javascript"></script>
    {{-- Md编辑器 --}}
    {!! editor_js_a() !!}
    <script type="text/javascript">
        $(function () {
            $("input[name='charge_way'][value='{{ $is_edit ? $train->get_charge->charge_way:'1' }}']").attr('checked','true');
            $("input[name='is_free'][value='{{ $is_edit ? $train->is_free:'0' }}']").attr('checked','true');
			$("input[name='is_card'][value='{{ $is_edit ? $train->get_charge->is_card:'0' }}']").attr('checked','true');
            $("input[name='is_health'][value='{{ $is_edit ? $train->get_charge->is_health:'0' }}']").attr('checked','true');
            $("input[name='is_labor'][value='{{ $is_edit ? $train->get_charge->is_labor:'0' }}']").attr('checked','true');
            $("input[name='is_learnership'][value='{{ $is_edit ? $train->get_charge->is_learnership:'0' }}']").attr('checked','true');
            $("#unit").val({{ $is_edit ? $train->get_charge->unit:'1' }});

            change_free({{ $is_edit ? $train->is_free:'0' }})
            var unit = "{{ $is_edit ? $train->get_charge->unit:'1' }}";
            if(unit =='2'){
                $(".max_nursery").show();
            }

            var charge_way = "{{ $is_edit }}";
            if(charge_way){
                var charge_way = "{{ $is_edit ? $train->get_charge->charge_way:'1' }}";
                $("input[name='attr1_name_"+charge_way+"']").val('{{ $is_edit ? $train->get_charge->attr1_name:'' }}');
                $("input[name='attr1_price_"+charge_way+"']").val('{{ $is_edit ? $train->get_charge->attr1_price:'' }}');
                $("input[name='attr2_name_"+charge_way+"']").val('{{ $is_edit ? $train->get_charge->attr2_name:'' }}');
                $("input[name='attr2_price_"+charge_way+"']").val('{{ $is_edit ? $train->get_charge->attr2_price:'' }}');
                $("input[name='attr3_name_"+charge_way+"']").val('{{ $is_edit ? $train->get_charge->attr3_name:'' }}');
                $("input[name='attr3_price_"+charge_way+"']").val('{{ $is_edit ? $train->get_charge->attr3_price:'' }}');
            }

            !function(a){a.fn.datepicker.dates["zh-CN"]={days:["星期日","星期一","星期二","星期三","星期四","星期五","星期六"],daysShort:["周日","周一","周二","周三","周四","周五","周六"],daysMin:["日","一","二","三","四","五","六"],months:["一月","二月","三月","四月","五月","六月","七月","八月","九月","十月","十一月","十二月"],monthsShort:["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"],today:"今日",clear:"清除",format:"yyyy年mm月dd日",titleFormat:"yyyy年mm月",weekStart:1}}(jQuery);
            $("#apply_start,#apply_end,#train_start,#train_end").datepicker({
                autoclose: true,
                todayHighlight: true,
                language:"zh-CN",
                format:"yyyy-mm-dd"
            });

            $("#unit").change(function () {
                var unit = $("#unit").val();
                if(unit ==1){
                    $("#max_nursery_num").val(0);
                    $(".max_nursery").hide();
                }else{
                    var charge_way1 = $("input[name='charge_way']:checked").val();
                    if(charge_way1 ==1){
                        $("#unit").val(1);
                        alert('单报团报方式不支持园所报名');
                        return false;
                    }
                    $(".max_nursery").show();
                }
            })
        })
        function change_free(t) {
            if(t){
                $('.no_free').show();
            }else{
                $(".max_nursery").hide();
                $('.no_free').hide();
            }
        }
        // 拖拽上传
        var inlineAttachmentOptions = {
            uploadUrl: '{{ route('article.uploadImage') }}',
            uploadFieldName: 'editormd-image-file',
            progressText: '![正在上传文件...]()',
            urlText: "\n ![file]({filename}) \n\n",
            extraParams: {
                "_token": '{{ csrf_token() }}'
            }
        };
        $('textarea').inlineattachment(inlineAttachmentOptions);
        // MKDown 编辑器
        $(function() {
            var editor = editormd("editormd");
        });

    </script>
@endsection