<?php

namespace App\Http\Controllers\Api;

use Exception;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NurseryStudents;
use App\Models\ApplyStudents;
use App\Repositories\Eloquent\NurseryStudentsRepositoryEloquent;

class StudentsController extends Controller
{
    //
    protected $nurseryStudentsRepositoryEloquent;

    public function __construct(NurseryStudentsRepositoryEloquent $nurseryStudentsRepositoryEloquent)
    {
        $this->nurseryStudentsRepositoryEloquent=$nurseryStudentsRepositoryEloquent;
    }
    public function nursery_students(Request $request){
        $validator = Validator::make($request->all(), [
            'contract_no'   => 'required',
            'apply_user'    =>'required',
            'train_id'      =>'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code'=>'1000',
                'msg'=>'参数错误',
                'data'=>''
            ]);
        }
        $contract_no = $request->get('contract_no','');
        $train_id    = $request->get('train_id','');
        $apply_user    = $request->get('apply_user','');
        $lists       = NurseryStudents::where('contract_no',$contract_no)
            ->where('apply_user',$apply_user)
            ->get()->toArray();
        $apply_students = ApplyStudents::where('contract_no',$contract_no)
            ->where('apply_user',$apply_user)
            ->where('train_id',$train_id)
            ->get()->toArray();
        $apply_students_ids = array_column($apply_students,'student_id');
        foreach($lists as $key=>$val){
            if( in_array($val['id'],$apply_students_ids) ){
                $lists[$key]['is_apply']=1;
            }else{
                $lists[$key]['is_apply']=0;
            }
        }
        return response()->json([
            'code'=>'200',
            'msg'=>'msg',
            'data'=>$lists
        ]);
    }
    public function save_nursery_students(Request $request){
        $validator = Validator::make($request->all(), [
            'apply_user'   => 'required',
            'contract_no'   => 'required',
            'student_name'  => 'required',
            'student_phone' => 'required',
            'student_sex'   => 'required',
            'student_position'=> 'required',
            'profession'    => 'required',
            'school'        => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code'=>'1000',
                'msg'=>'信息不完整',
                'data'=>''
            ]);
        }
		$count = NurseryStudents::where('apply_user',$request->get('apply_user'))
            ->where('contract_no',$request->get('contract_no'))
            ->where('student_phone',$request->get('student_phone'))
            ->count();
        if($count){
            return response()->json([
                'code'=>'1000',
                'msg'=>'重复添加',
                'data'=>''
            ]);
        }
        $id = $this->nurseryStudentsRepositoryEloquent->saveStudents($request);
        if($id){
            return response()->json([
                'code'=>'200',
                'msg'=>'ok',
                'data'=>[
                    'id'=>$id
                ]
            ]);
        }else{
            return response()->json([
                'code'=>'1009',
                'msg'=>'添加失败',
                'data'=>[]
            ]);
        }
    }
    public function nursery_students_edit($id){
        $info = $this->nurseryStudentsRepositoryEloquent->find($id);
        if($info){
            return response()->json([
                'code'=>'200',
                'msg'=>'ok',
                'data'=>$info
            ]);
        }else{
            return response()->json([
                'code'=>'1004',
                'msg'=>'未找到',
                'data'=>[]
            ]);
        }
    }
    public function nursery_students_update(Request $request){
        $validator = Validator::make($request->all(), [
            'id'            => 'required',
//            'contract_no'   => 'required',
            'student_name'  => 'required',
            'student_phone' => 'required',
            'student_sex'   => 'required',
            'student_position'=> 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'code'=>'1000',
                'msg'=>'参数错误',
                'data'=>''
            ]);
        }
        $result = $this->nurseryStudentsRepositoryEloquent->update($request->all(),$request->input('id'));
        if($result){
            return response()->json([
                'code'=>'200',
                'msg'=>'ok',
                'data'=>[]
            ]);
        }else{
            return response()->json([
                'code'=>'1009',
                'msg'=>'更新失败',
                'data'=>[]
            ]);
        }
    }
    public function save_apply_students(Request $request){
        $students = $request->get('student_id');
        $contract_no = $request->get('contract_no','');
        $train_id    = $request->get('train_id','');
        $apply_user  = $request->get('apply_user');

        ApplyStudents::where('contract_no',$contract_no)
            ->where('apply_user',$apply_user)
            ->where('train_id',$train_id)
            ->delete();
        if($students){
            $students = explode(',',$students);
            foreach($students as $student){
                ApplyStudents::create([
                    'student_id' =>$student,
                    'apply_user' =>$apply_user,
                    'contract_no'=>$contract_no,
                    'train_id'   =>$train_id
                ]);
            }
        }

        return response()->json([
            'code'=>'200',
            'msg'=>'ok',
            'data'=>[]
        ]);
    }
    public function apply_students_del($id){
        if( ApplyStudents::where('id',$id)->delete() ){
            return response([
                'code'=>'200',
                'msg'=>'ok',
                'data'=>[]
            ]);
        }else{
            return response([
                'code'=>'1010',
                'msg'=>'删除失败',
                'data'=>[]
            ]);
        }
    }
}
