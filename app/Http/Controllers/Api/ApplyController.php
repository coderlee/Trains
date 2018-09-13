<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\WxUser;
use App\Models\NurseryStudents;
use App\Models\Order;
use App\Models\ApplyStudents;
use App\Models\OrderStudents;

class ApplyController extends Controller
{
    //
    public function bind_phone(Request $request){
        $open_id = $request->get('open_id','');
        $mobile  = $request->get('mobile','');
        $code    = $request->get('code','');

        //code验证

        if( WxUser::where('open_id',$open_id)->update(['mobile'=>$mobile]) ){
            return ['code'=>200,'msg'=>'ok'];
        }
    }

    /**
     * @param Request $request
     * @return array
     * 验证园所合同号 取出已添加学员
     */
    public function check_contract(Request $request){
        $train_id    = $request->get('train_id','');
        $open_id     = $request->get('open_id','');
        $contract_no = $request->get('contract_no','');
        $type        = $request->get('type','');
        $info = $this->get_contract($contract_no);
        if($info){
            WxUser::where('open_id',$open_id)->update(['contract_no'=>$contract_no]);

            $students = ApplyStudents::where('contract_no',$contract_no)
                ->where('train_id',$train_id)
                ->get();
//            if(!empty($students)){
//                foreach($students as $val){
//                    $is_paid = Order::where('is_paid',1)
//                        ->where('contract_no',$contract_no)
//                        ->where('train_id',$train_id)
//                        ->join('t_order_students','t_train_order.id','=','t_order_students.order_id')
//                        ->get();
//                }
//            }
            return ['contract_no'=>$info,'students'=>$students];
        }
    }
    /**
     * 请求基础平台，获取园所信息
     */
    private function get_contract($contract_no,Request $request){
        $data = [];
        $data['key'] = '27a15511082d11e6b23b00163e005ebf';
        $data['dt']  = 'json';
        $data['code']=$contract_no;
        $url = "http://base.rybbaby.com/api/base/schoolInfo";
        $info = $request::create($url,'post',$data);
        return $info;
    }

    /**
     * 添加园所学员
     */
    public function add_student(Request $request){
        $id = NurseryStudents::created($request->all())->id;
        return $id;
    }
    /**
     * 确认学员
     */
    public function confirm_students(Request $request){
        $train_id = $request->get('train_id','');
        $contract_no=$request->get('contract_no','');
        $students = $request->get('students','');
        foreach($students as $val){
            ApplyStudents::insert( ['student_id'=>$val['student_id'],'contract_no'=>$contract_no,'train_id'=>$train_id] );
        }
        return ['code'=>200,'msg'=>'ok'];
    }


}
