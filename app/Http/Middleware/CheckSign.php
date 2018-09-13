<?php

namespace App\Http\Middleware;

use Closure;

class CheckSign
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $timestamp = $request->timestamp;
        $randomStr = $request->randomStr;
        $sign      = $request->sign;
        if(!$timestamp || !$randomStr || !$sign){
            return response()->json(['code'=>'1000','msg'=>'参数缺少','data'=>[]]);
        }
        $secret    = config('wechat.mini')['secret'];
        if( time()- $timestamp >30){
            return response()->json(['code'=>'1001','msg'=>'timestamp超时','data'=>[]]);
        }
        $str = md5($timestamp.$secret.$randomStr);
        $str = substr($str, 0, -1);
        if($str == $sign){
            return $next($request);
        }else{
            return response()->json(['code'=>'1002','msg'=>'签名失败','data'=>[]]);
        }
    }
}
