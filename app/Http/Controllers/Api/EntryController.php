<?php

namespace App\Http\Controllers\Api;

use App\Models\Trains;
use function GuzzleHttp\default_ca_bundle;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\EntryPost;
use App\Repositories\Eloquent\EntryRepositoryEloquent;
use App\Repositories\Eloquent\StudentsRepositoryEloquent;
use App\Models\Entry;
use App\Models\ApplyStudents;
use App\Models\WxUser;
use App\Models\Students;
use App\Models\PayInfo;
use Illuminate\Support\Facades\DB;
use Exception;
use EasyWeChat\Factory;
use Log;

class EntryController extends Controller
{
    protected $entryRepositoryEloquent;
    protected $studentsRepositoryEloquent;
    private $config;

    public function __construct(EntryRepositoryEloquent $entryRepositoryEloquent,StudentsRepositoryEloquent $studentsRepositoryEloquent)
    {
        $this->entryRepositoryEloquent=$entryRepositoryEloquent;
        $this->studentsRepositoryEloquent=$studentsRepositoryEloquent;
        $this->config=config('wechat.mini');
    }
    public function get_orders(Request $request){
        $lists = Entry::where(function ($query) use ($request){
            if( $request->has('is_paid') ){
                $query->where('is_paid',$request->get('is_paid'));
				$query->where('status','!=','2');
            }
        })
            ->where('apply_user',$request->get('apply_user',''))
			->orderBy('id','desc')
            ->with(['get_train','get_students'])
            ->get();
        $lists = $lists->toArray();
        foreach($lists as $key=>$list){
            $lists[$key]['students'] = Students::where('order_id',$list['id'])->count();
        }

        return response()->json(['code'=>'200','msg'=>'ok','data'=>$lists]);
    }
    public function order_detail($id){
        $info = Entry::with([
            'get_train'=>function($query){
                $query->with('get_charge');
            },
            'get_students'=>function($query){
                $query->with('get_nursery_user');
            }
        ])->find($id);
        if($info->status=='0' && $info->is_paid=='0'){
            $info->surplus = intval(strtotime($info->created_at)+30*60-time());
        }
        return response()->json([
            'code'=>'200',
            'msg'=>'ok',
            'data'=>$info
        ]);
    }
    public function cancel_order($id){
        if( $this->entryRepositoryEloquent->update(['status'=>2],$id) ){
            return response()->json([
                'code'=>'200',
                'msg'=>'ok',
                'data'=>[]
            ]);
        }else{
            return response()->json([
                'code'=>'1010',
                'msg'=>'取消失败',
                'data'=>[]
            ]);
        }
    }
    public function save_order(Request $request){
        $data       = $request->instance();
        $train_id   = $request->get('train_id','');
        $contract_no=$request->get('contract_no','');
        $apply_user =$request->get('apply_user','');
        //报名学员
        $applyStudents = ApplyStudents::where('contract_no',$contract_no)
			->where('apply_user',$apply_user)
            ->where('train_id',$train_id)
            ->with('get_student')
            ->get();
        if(!count($applyStudents)){
            return response()->json(['code'=>'1011','msg'=>'已无报名学员','data'=>[]]);
        }
        $data['apply_num'] = count($applyStudents);
        //培训信息
        $train_info = Trains::with('get_charge')->find($train_id);
		if($train_info->apply_start >date("Y-m-d")){
			return response()->json(['code'=>'1018','msg'=>'报名未开始','data'=>[]]);
		}
		if($train_info->apply_end <date("Y-m-d")){
			return response()->json(['code'=>'1018','msg'=>'报名已结束','data'=>[]]);
		}
        if($train_info->pre_num < $data['apply_num']){
            return response()->json(['code'=>'1012','msg'=>'剩余名额不足','data'=>[]]);
        }
        if($train_info->is_free){//付费
            $data['status'] =0;
            $data['is_paid']=0;
            switch ($train_info->get_charge->charge_way){
                case 1:
					if(!$train_info->get_charge->attr2_price){
						$group_price = $train_info->get_charge->attr1_price;
						$price       = $train_info->get_charge->attr1_price;  
					}else{
						if($train_info->get_charge->attr1_price > $train_info->get_charge->attr2_price){
							$price = $train_info->get_charge->attr1_price;
							$group_price = $train_info->get_charge->attr2_price;
						}else{
							$price = $train_info->get_charge->attr2_price;
							$group_price = $train_info->get_charge->attr1_price;
						}
					}
                    
                    if($train_info->get_charge->min_num && $data['apply_num'] >= $train_info->get_charge->min_num){
                        $data['apply_form'] = 2;
                        $data['fee'] = $group_price;
                        $data['total_fee']  =($data['apply_num']) * $group_price;
                    }else{
                        $data['apply_form'] = 1;
                        $data['fee'] = $price;
                        $data['total_fee']  =($data['apply_num']) * $price;
                    }
                    break;
                case 2:
                    if($train_info->get_charge->unit =='2' && $data['apply_num'] > $train_info->get_charge->max_nursery_num){
                        return response()->json(['code'=>'1014','msg'=>'报名人数超出限制','data'=>[]]);
                    }
                    $tmp = [$train_info->get_charge->attr1_price,$train_info->get_charge->attr2_price,$train_info->get_charge->attr3_price];
                    $min_price = min($tmp);
                    if($train_info->get_charge->unit =='2'){
                        $data['apply_form'] = 2;
                        $data['fee']        = $min_price/$data['apply_num'];
                        $data['total_fee']  = $min_price;
                    }elseif($train_info->get_charge->unit =='1'){
                        $data['apply_form'] = 1;
                        $data['fee'] = $min_price;
                        $data['total_fee']  =($data['apply_num']) * $min_price;
                    }
                    break;
                case 3:
                    if($train_info->get_charge->unit =='2' && $data['apply_num'] > $train_info->get_charge->max_nursery_num){
                        return response()->json(['code'=>'1014','msg'=>'报名人数超出限制','data'=>[]]);
                    }
                    $price = $request->get('price','');
                    if($train_info->get_charge->unit =='2'){
                        $data['apply_form'] = 2;
                        $data['fee']        = $price/$data['apply_num'];
                        $data['total_fee']  = $price;
                    }elseif($train_info->get_charge->unit =='1'){
                        $data['apply_form'] = 1;
                        $data['fee'] = $price;
                        $data['total_fee']  =($data['apply_num']) * $price;
                    }
                    break;
                default:
                    break;
            }
        }else{//免费
            $data['fee']       = 0;
            $data['total_fee'] = 0;
            $data['apply_form']= 1;
            $data['is_paid']   = 1;
            $data['status']    = 3;
        }



        //报名人
        $apply_info =WxUser::find($apply_user);
        $data['apply_user_name']=$apply_info->nick_name;
        $data['mobile']         =$apply_info->mobile;
        //开始事务
        DB::beginTransaction();
        try{
            $train_order_id = $this->entryRepositoryEloquent->saveApiOrder($data);
            if($train_order_id){
                $data['order_id'] = $train_order_id;
                foreach($applyStudents as $applyStudent){
                    $data['student_id'] = $applyStudent['student_id'];
                    //$this->studentsRepositoryEloquent->saveApiOrderStudent($data);
					Students::create([
                        'order_id'   => $data['order_id'],
                        'student_id' => $data['student_id'],
                        'fee'        => $data['fee'],
                        'is_paid'    => $data['is_paid'],
                        'status'     => $data['status']
                    ]);
                }
            }
            ApplyStudents::where('contract_no',$contract_no)
				->where('apply_user',$apply_user)
                ->where('train_id',$train_id)
                ->delete();
            DB::commit();

            return response()->json([
                'code'=>'200',
                'msg'=>'ok',
                'data'=>[
                    'order_id'=>$train_order_id
                ]
            ]);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage());
        }
    }
    //去支付
    public function go_pay(Request $request){
        $order_id = $request->get('order_id','');
        $order_info = Entry::with(['get_user','get_train'])->find($order_id);
        if(!$order_info->get_train->is_free){
            return response()->json(['code'=>'1013','msg'=>'培训不需要支付','data'=>[]]);
        }
        if($order_info->is_paid =='1' || $order_info->status !=0){
            return response()->json(['code'=>'1013','msg'=>'状态异常','data'=>[]]);
        }
        //微信支付
        $miniProgram = Factory::payment($this->config);
        $result = $miniProgram->order->unify([
            'body' => '红黄蓝课程培训',
            'out_trade_no' => $order_info->order_sn,
            'total_fee'    => $order_info->total_fee*100,
            'trade_type'   => 'JSAPI',
            'openid'       => $order_info->get_user->open_id,
        ]);
        if(isset($result['result_code']) && $result['result_code'] =='SUCCESS'){
            $arr = array();
            $arr['appId'] = $result['appid'];
            $arr['nonceStr'] = $result['nonce_str'];
            $arr['package'] = "prepay_id=".$result['prepay_id'];
            $arr['signType'] = "MD5";
            $arr['timeStamp'] = (string)time();
            $str = $this->ToUrlParams($arr);
            $jmstr = $str."&key=".$this->config['key'];
            $arr['paySign'] = strtoupper(MD5($jmstr));
			$arr['prepay_id']=$result['prepay_id']; 
            return response()->json($arr);
        }else{
            return response()->json(['code'=>'1016','msg'=>'调取支付失败']);
        }
    }
	//审核未通过，重新激活订单
    public function activate_order($id){
        try{
            DB::beginTransaction();
            $entry = Entry::find($id);
            if($entry->status =='4' || $entry->status =='5'){
                Entry::where('id',$id)->update(['status'=>3]);
                Students::where('order_id',$entry->id)->update(['status'=>0]);
            }elseif($entry->status ==6){
                return response()->json([
					'code'=>'1019',
                    'msg'=>'已审核通过',
                    'data'=>[]
                ]);
            }else{
                return response()->json([
                    'code'=>'1013',
                    'msg'=>'报名状态异常',
                    'data'=>[]
                ]);
            }
            DB::commit();
            return response()->json([
                'code'=>'200',
                'msg'=>'ok',
                'data'=>[]
            ]);
        }catch (Exception $e){
            DB::rollBack();
            return response()->json($e->getMessage());
        }

    }
    //构建字符串
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v)
        {
            if($k != "sign"){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }
    //支付回調
    public function notify(){
        $miniProgram = Factory::payment($this->config);
        $response = $miniProgram->handlePaidNotify(function ($message, $fail) {
			Log::error('order notify info '.json_encode($message));
            $order_sn = $message['out_trade_no'];
            //$order_info = $this->entryRepositoryEloquent->findByField('order_sn',$order_sn);
			$order_info = Entry::where('order_sn',$order_sn)->first();
			//Log::error('order info '.json_encode($order_info));
            if ($message['return_code'] === 'SUCCESS') {
                // 用户是否支付成功
                if (array_get($message, 'result_code') === 'SUCCESS') {
                    $count = PayInfo::where('trade_no',array_get($message,'transaction_id'))->count();
					if(!$count){
						$order_info->is_paid =1;
						$order_info->status  =3;
						$order_info->pay_time=date("Y-m-d H:i:s");
						$order_info->save();
						Students::where('order_id',$order_info->id)->update(['is_paid'=>1]);

						PayInfo::create([
							'order_sn' =>array_get($message,'out_trade_no'),
							'trade_no' =>array_get($message,'transaction_id'),
							'total_fee'=>array_get($message,'total_fee')/100,
							'pay_time' =>array_get($message,'time_end'),
							'openid'   =>array_get($message,'openid'),
						]);
						//减库存,加销量
						Trains::where('id',$order_info->train_id)->decrement('pre_num',$order_info->apply_num);
						Trains::where('id',$order_info->train_id)->increment('sale_num',$order_info->apply_num);

						//发送模板消息
						/*
						$app = Factory::miniProgram($this->config);
						$app->template_message->send([
							'touser' => array_get($message,'openid'),
							'template_id' => 'template-id',
							'page' => 'index',
							'form_id' => 'form-id',
							'data' => [
								'keyword1' => 'VALUE',
								'keyword2' => 'VALUE2',
								// ...
							],
						]);
						*/
						return true; 
					}
                } elseif (array_get($message, 'result_code') === 'FAIL') {
					return $fail('pay fail.');
                }
            }else{
                return $fail('通信失败，请稍后再通知我');
            }
        });
        return $response;
    }
	/**
     * 发送消息模板
     */
    public function send_template(Request $request){
        $order_id = $request->get('order_id','');
        $prepay_id= $request->get('prepay_id','');
        if(!$order_id || !$prepay_id){
            return response()->json([
                'code'=>'1000',
                'msg'=>'缺少参数',
                'data'=>[]
            ]);
        }
        $order_info = Entry::with(['get_train','get_user'])->find($order_id);
        if($order_info->is_paid =='1'){
            //发送模板消息
            $app = Factory::miniProgram($this->config);
            $res = $app->template_message->send([
                'touser' => $order_info->get_user->open_id,
                'template_id' => '_fa2cDJVj6QNuobc_gKCj_tilgw6MnWiW4j5q_WR0eo',
                'page' => 'pages/my/index',
                'form_id' => $prepay_id,
                'data' => [
                    'keyword1' => $order_info->get_train->title,
                    'keyword2' => $order_info->get_train->train_start,
                    'keyword3' => $order_info->get_train->train_adress,
                    'keyword4' => $order_info->total_fee,
                    'keyword5' => $order_info->pay_time,
                ],
            ]);
            return response()->json($res);
        }else{
            return response()->json(['code'=>'1017','msg'=>'订单未支付']);
        }

    }
}
