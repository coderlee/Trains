<?php

namespace App\Http\Controllers\Admin;

use function GuzzleHttp\default_ca_bundle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Students;
use App\Models\Entry;
use App\Models\Trains;
use App\Repositories\Eloquent\StudentsRepositoryEloquent;
use Auth;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentsController extends Controller
{
    protected $studentsRepositoryEloquent;

    public function __construct(StudentsRepositoryEloquent $studentsRepositoryEloquent)
    {
        $this->studentsRepositoryEloquent = $studentsRepositoryEloquent;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $trains = Trains::where('status',2)->get();
        $lists = Students::where(function($query) use($request){
            if($request->has('status')){
                $query->where('status',$request->get('status'));
            }
        })
            ->whereHas('get_order',function ($query) use ($request){
                if($request->has('contract_no')){
                        $query->where('contract_no',$request->get('contract_no'));
                }
                if($request->has('train_id')){
                    $query->where('train_id',$request->get('train_id'));
                }
                if($request->has('order_id')){
                    $query->where('id',$request->get('order_id'));
                }
            })
            ->whereHas('get_nursery_user',function ($query) use ($request){
                if($request->has('student_phone')){
                        $query->where('student_phone',$request->get('student_phone'));
                }
            })
            ->with(['get_order'=>function($query){
                $query->with('get_train');
            },'get_nursery_user'])
            ->where('is_paid','1')
            ->orderBy('created_at','desc')
            ->paginate(10);
        //dd($lists->toArray());
        return view('admin.students.index',['trains'=>$trains,'lists'=>$lists,'search'=>$request->toArray()]);
    }
    /**
     * 审核
     */
    public function check(Request $request){
        $id = $request->get('order_students_id','');
        $status = $request->get('status','');
        $remark = $request->get('remark','');
        if($id){
            $order_students_info = Students::find($id);
            if($order_students_info){
                if($order_students_info->status !=0){
                    return response()->json(['code'=>'0','msg'=>'状态异常']);
                }
                if($order_students_info->is_paid !=1){
                    return response()->json(['code'=>'0','msg'=>'未支付']);
                }
                $order_students_info->status = $status;
                $order_students_info->remark = $remark;
                $order_students_info->check_recoder = Auth::user()->name;
                $order_students_info->check_time = date("Y-m-d H:i:s");
                $order_students_info->save();
				
				$entry = Entry::find($order_students_info->order_id);
                if($entry->apply_num ==1){
					if($status == '1'){
						$entry->status =6;
					}else{
						$entry->status =4;
					}
                }else{
                    //未通过人数
                    $w_count = Students::where('order_id',$entry->id)->where('status',2)->count();
					if($w_count){
						$entry->status =4;
					}else{
						$entry->status =6;
					}
                }
				$entry->remark = $remark;
				$entry->save();
				return ['code'=>200,'msg'=>'操作完成!'];
            }

        }
    }
    /**
     * 签到
     */
    public function sign(Request $request){
        $id = $request->get('order_students_id','');
        if($id){
            $order_students_info = Students::where('id',$id)
                ->where('status','1')
                ->where('is_paid',1)
                ->first();
            if($order_students_info){
                $order =Entry::where('id',$order_students_info->order_id)
                    ->where('status',0)
                    ->first();
                if($order->is_paid !=1){
                    return ['code'=>0,'msg'=>'未支付'];
                }

                $order_students_info->status=3;
                $order_students_info->sign_time=date("Y-m-d H:i:s");
                $order_students_info->save();
                return ['code'=>200,'msg'=>'签到成功!'];
            }
            return ['code'=>0,'msg'=>'数据异常'];
        }
        return ['code'=>0,'msg'=>'数据异常'];
    }
    /**
     * 退训
     */
    public function refund(Request $request){
        $id = $request->get('rid','');
        $remark = $request->get('remark','');
        if($id){
            $order_students_info = Students::where('id',$id)
                ->where('is_paid',1)
                ->first();
            if($order_students_info){
                $order =Entry::where('id',$order_students_info->order_id)->first();
                if($order->is_paid !='1'){
                    return ['code'=>0,'msg'=>'未支付'];
                }
                if($order->status=='-1'){
                    return ['code'=>0,'msg'=>'已退训'];
                }
                if($order->from ==2){
					$order->status=1;
				}
                $order->remark = $remark;
                $order->save();

                $order_students_info->status='-1';
                $order_students_info->save();
                //返还库存 减销量
				if($order->from ==2){
					Trains::where('id',$order->train_id)->increment('pre_num');
					Trains::where('id',$order->train_id)->decrement('sale_num');
				}
                return ['code'=>200,'msg'=>'退训申请已提交!'];
            }
            return ['code'=>0,'msg'=>'数据异常'];
        }
        return ['code'=>0,'msg'=>'数据异常'];
    }
    /**
     * 学员信息
     */
    public function info(Request $request){
        $order_student_id = $request->input('order_students_id','');
        if($order_student_id){
            $info = Students::with('get_nursery_user')->find($order_student_id);
            return response()->json([
                'code'=>'200',
                'msg' =>'ok',
                'data'=>$info
            ]);
        }
    }
    /**
     * 学员导出
     */
    public function export_data(Request $request){
        $lists = Students::where(function($query) use($request){
            if($request->input('status') !==null ){
                $query->where('status',$request->get('status'));
            }
        })
            ->whereHas('get_order',function ($query) use ($request){
                if($request->input('contract_no')){
                    $query->where('contract_no',$request->get('contract_no'));
                }
                if($request->input('park_name')){
                    $query->where('park_name',$request->get('park_name'));
                }
                if($request->input('train_id')){
                    $query->where('train_id',$request->get('train_id'));
                }
            })
            ->whereHas('get_nursery_user',function ($query) use ($request){
                if($request->input('student_phone')){
                    $query->where('student_phone',$request->get('student_phone'));
                }
            })
            ->with(['get_order'=>function($query){
                $query->with('get_train');
            },'get_nursery_user'])
            ->where('is_paid','1')
            ->orderBy('created_at','desc')
            ->get();
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle('学员表');

        $worksheet->setCellValueByColumnAndRow(1, 1, '园所合同号');
        $worksheet->setCellValueByColumnAndRow(2, 1, '园所名称');
        $worksheet->setCellValueByColumnAndRow(3, 1, '培训主题');
        $worksheet->setCellValueByColumnAndRow(4, 1, '学员姓名');
        $worksheet->setCellValueByColumnAndRow(5, 1, '学员性别');
        $worksheet->setCellValueByColumnAndRow(6, 1, '学员手机号');
        $worksheet->setCellValueByColumnAndRow(7, 1, '学员岗位');
        $worksheet->setCellValueByColumnAndRow(8, 1, '签到日期');
        $worksheet->setCellValueByColumnAndRow(9, 1, '培训状态');

        for($i=0;$i<count($lists);$i++){
            $j =$i+2;
            $worksheet->setCellValueByColumnAndRow(1,$j,$lists[$i]->get_order->contract_no);
            $worksheet->setCellValueByColumnAndRow(2,$j,$lists[$i]->get_order->park_name);
            $worksheet->setCellValueByColumnAndRow(3,$j,$lists[$i]->get_order->get_train->title);
            $worksheet->setCellValueByColumnAndRow(4,$j,$lists[$i]->get_nursery_user->student_name);
            $worksheet->setCellValueByColumnAndRow(5,$j,$lists[$i]->get_nursery_user->student_sex==1?'男':'女');
            $worksheet->setCellValueByColumnAndRow(6,$j,$lists[$i]->get_nursery_user->student_phone);
            $worksheet->setCellValueByColumnAndRow(7,$j,$lists[$i]->get_nursery_user->student_position);
            $worksheet->setCellValueByColumnAndRow(8,$j,$lists[$i]->sign_time);
            $worksheet->setCellValueByColumnAndRow(9,$j,$this->text_status($lists[$i]->status) );
        }
        $filename = '培训报名表.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        echo $writer->save('php://output');die();

    }
    private function text_status($status){
        switch($status){
            case 0:
                return '未审核';
                break;
            case 1:
                return '审核通过未签到';
                break;
            case 2:
                return '审核未通过';
                break;
            case 3:
                return '已签到';
                break;
            case 4:
                return '已完成';
                break;
            default:
                return '已退训';
                break;
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
