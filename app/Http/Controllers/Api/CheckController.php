<?php

namespace App\Http\Controllers\Api;

use App\Models\NurseryStudents;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\PhoneCode;
use App\Models\ApplyStudents;
use App\Models\WxUser;
use App\Models\Trains;
use App\Http\Controllers\Controller;
use Log;

class CheckController extends Controller
{
    //
    public function check_contract(Request $request){
        $contract_no = $request->get('contract_no','');
        $train_id    = $request->get('train_id','');
        $apply_user  = $request->get('apply_user','');
        if(!$contract_no || !$train_id || !$apply_user){
            return response()->json([
                'code'=>'1000',
                'msg'=>'缺少参数',
                'data'=>[]
            ]);
        }
        if($contract_no){
            $data =[];
            $data['key'] = '27a15511082d11e6b23b00163e005ebf';
            $data['dt']  = 'json';
            $data['code']= $contract_no;

            $http = new Client();
            $response = $http->request('post','http://base.rybbaby.com/api/base/schoolInfo',['form_params'=>$data]);
            $data = json_decode((string)$response->getBody(), true);
            if($data['result']){
                $applyStudents = ApplyStudents::where('contract_no',$contract_no)
                    ->where('apply_user',$apply_user)
                    ->where('train_id',$train_id)
                    ->with('get_student')
                    ->get();
                $train = Trains::with('get_charge')->find($train_id);
                $student_count = NurseryStudents::where('contract_no',$contract_no)
                    ->where('apply_user',$apply_user)
                    ->count();
                return response()->json([
                        'code'=>'200',
                        'msg'=>'ok',
                        'data'=>[
                            'contract'      =>$data['json'],
                            'apply_students'=>$applyStudents,
                            'train_info'    =>$train,
                            'student_count' =>$student_count
                        ]
                    ]);
            }else{
                return response()->json(['code'=>'1004','msg'=>'未找到','data'=>[]]);
            }
        }
    }

    public function send_code(Request $request){
        header('content-type:text/html;charset=utf-8');
        $phone =$request->get('apply_phone','');
        $info = PhoneCode::where('phone',$phone)
            ->orderBy('send_time','desc')
            ->first();
        if(!empty($info) && $info->status==0 && $info->next_time>time() ){
            return response()->json([
                'code'=>'1008',
                'msg'=>'歇一会再试',
                'data'=>[
                    'exipre'=>$info->next_time-time()
                ]
            ]);
        }

        $sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
        $code = rand(111, 999).rand(111,999);
        $smsConf = array(
            'key' => 'f8d345645cfd6b9b583007db8018a60a', //您申请的APPKEY
            'mobile' => $phone . '', //接受短信的用户手机号码
            //'tpl_id' => '87049', //您申请的短信模板ID，根据实际情况修改
            'tpl_id' => '91412', //您申请的短信模板ID，根据实际情况修改
            'tpl_value' => "#code#=".$code //您设置的模板变量，根据实际情况修改
        );
        PhoneCode::where('phone',$phone)->update(['status'=>1]);
        $content = $this->juhecurl($sendUrl, $smsConf, 1); //请求发送短信
        if ($content) {
            $result = json_decode($content, true);
            $error_code = $result['error_code'];
            if ($error_code == 0) {//成功
                $sendTime = time();
                PhoneCode::create([
                    'phone'=>$phone,
                    'code'=>$code,
                    'send_time'=>$sendTime,
                    'dead_time'=>$sendTime+10*60,
                    'next_time'=>$sendTime+60,
                    'status'=>0
                ]);
                return response()->json([
                    'code'=>'200',
                    'msg' =>'ok',
                    'data'=>[]
                ]);
            }else{//失败
                return response()->json([
                    'code'=>'1005',
                    'msg' =>'发送失败',
                    'data'=>[]
                ]);
            }
        }
    }
    public function check_code(Request $request){
        $phone = $request->get('apply_phone','');
        $code  = $request->get('code','');
        $openId= $request->get('open_id','');
		Log::error('check_code openId:'.$openId.' apply_phone :'.$phone);
        $info = PhoneCode::where('status','0')
            ->where('phone',$phone)
            ->orderBy('send_time','desc')
            ->first();
		if(!$info){
			return response()->json([
                'code'=>'1007',
                'msg' =>'请先获取验证码',
                'data'=>[]
            ]);
		}
        if($info && $info->dead_time<time() ){
            return response()->json([
                'code'=>'1007',
                'msg' =>'验证码过期',
                'data'=>[]
            ]);
        }
        if($info->code ==$code){
            $info->status=1;
            $info->save();

            //绑定手机号
            WxUser::where('open_id',$openId)->update(['mobile'=>$phone]);
            WxUser::where('id',$openId)->update(['mobile'=>$phone]);
            return response()->json([
                'code'=>200,
                'msg' =>'ok',
                'data'=>[]
            ]);
        }else{
            return response()->json([
                'code'=>'1006',
                'msg' =>'验证码错误',
                'data'=>[]
            ]);
        }
    }
    private function juhecurl($url, $params = false, $ispost = 0)
    {
        //exit;
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
}
