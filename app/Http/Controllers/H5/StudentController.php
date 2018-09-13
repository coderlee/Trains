<?php

namespace App\Http\Controllers\H5;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Students;
use App\Models\NurseryStudents;
use App\Models\Entry;
use App\Models\PhoneCode;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //http://train.com/h5/student?id=2
        $train_id = $request->get('id','');
        return view('h5.check_student',['train_id'=>$train_id]);
    }
    /**
     * 签到成功
     */
    public function sign_success(){
        return view('h5.sign_success');
    }
    /**
     * 签到失败
     */
    public function sign_error(){
        return view('h5.sign_error');
    }
    /**
     * 验证学员
     */
    public function check_student(Request $request){
        $code          = $request->get('code','');
        $student_phone = $request->get('student_phone','');
		/*
        if(!$code && !$student_phone){
            return response()->json(['code'=>'1000','msg'=>'缺少参数']);
        }
        $is_true = PhoneCode::where('phone',$student_phone)
            ->where('status',0)
            ->orderBy('send_time','desc')
            ->first();
        if($is_true->code !=$code){
            return response()->json(['code'=>'1006','msg'=>'验证码错误']);
        }
        if($is_true->dead_time < time()){
            return response()->json(['code'=>'1007','msg'=>'验证码过期']);
        }*/
        $order_student_info = Students::where('is_paid',1)
            ->whereHas('get_order',function ($query) use($request){
                $query->where('is_paid',1)->where('train_id',$request->get('train_id'));
            })
            ->whereHas('get_nursery_user',function ($query) use($request){
                $query->where('student_phone',$request->get('student_phone',''));
            })
			->with([
				'get_nursery_user',
				'get_order'=>function($query) use($request){
					$query->with('get_train');
				}
			])
            ->first();
			/*
        if($order_student_info->status !=1){
            return response()->json(['code'=>'0','msg'=>'状态异常']);
        }*/
        $order_student_info->status    =3;
        $order_student_info->sign_time =date("Y-m-d H:i:s");
        //$order_student_info->save();
        return response()->json(['code'=>'200','msg'=>'ok','data'=>$order_student_info]);
    }
    /**
     * 发送验证码
     */
    public function send_code(Request $request){
        header('content-type:text/html;charset=utf-8');
        $phone =$request->get('student_phone','');
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
