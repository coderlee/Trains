<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trains;
use App\Models\TrainCharge;
use Cache;

class TrainController extends Controller
{
    //
    public function trains(){
        //if(Cache::has('Trains.lists')){
            //$lists = Cache::get('Trains.lists');
        //}else{
            $lists = Trains::where('status',2)
                ->with('get_charge')
                ->orderBy('created_at','desc')
                ->orderBy('sort','desc')
                ->get()->toArray();
            //Cache::forever('Trains.lists',$lists);
        //}
        foreach($lists as $key=>$val){
            $nowDate = date("Y-m-d");
            if($val['apply_start'] >$nowDate){
                $lists[$key]['state'] ='报名未开始';
            }elseif($val['apply_start'] <=$nowDate && $val['apply_end'] >=$nowDate){
                $lists[$key]['state'] ='报名中';
            }elseif ($val['apply_end'] <$nowDate && $val['train_start']>$nowDate){
                $lists[$key]['state'] ='报名已结束';
            }elseif($val['train_start']<=$nowDate && $val['train_end']>=$nowDate){
                $lists[$key]['state'] ='培训中';
            }elseif($val['train_end'] <$nowDate){
				$lists[$key]['state'] ='培训结束';
			}
        }
        return response()->json([
            'code'=>'200',
            'msg'=>'ok',
            'data'=>$lists
        ]);
    }

    public function show($id){
        //if(Cache::has("Trains.info-'.$id.'")){
            //$info = Cache::get("Trains.info-'.$id.'");
        //}else{
            $info = Trains::where("status",2)
                ->with('get_charge')
                ->findOrFail($id);
            //Cache::forever("Trains.info-'.$id.'",$info);
        //}
        return response()->json([
            'code'=>'200',
            'msg'=>'ok',
            'data'=>$info
        ]);
    }
	//资料上传设置
    public function train_setting($id){
        $info = TrainCharge::where('train_id',$id)->select('is_card','is_health','is_labor','is_learnership')->first();
        if($info){
            return response()->json([
                'code'=>'200',
                'msg'=>'ok',
                'data'=>$info
            ]);
        }
    }
}
