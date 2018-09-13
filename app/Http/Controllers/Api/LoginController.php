<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Factory;
use App\Models\WxUser;

class LoginController extends Controller
{
    private $config;

    function __construct()
    {
        $this->config=config('wechat.mini');
    }

    //
    public function get_session_key(Request $request){
        $miniProgram = Factory::miniProgram($this->config);

        $code =$request->get('code');
        $session = $miniProgram->auth->session($code);
        return response()->json(['code'=>'200','msg'=>'ok','data'=>$session]);
    }
    public function auth_login(Request $request){
        $encryptedData = $request->get('encryptedData','');
        $iv            = $request->get('iv','');
        $sessionKey    = $request->get('sessionKey','');

        $miniProgram = Factory::miniProgram($this->config);
        $data =$miniProgram->encryptor->decryptData($sessionKey, $iv, $encryptedData);
        $id = WxUser::updateOrCreate(
            ['open_id'=>$data['openId']],
            ['nick_name'=>$this->removeEmoji($data['nickName']),'avatar_url'=>$data['avatarUrl'],'city'=>$data['city'],'country'=>$data['country'],'province'=>$data['province'],'gender'=>$data['gender'],'app_id'=>$this->config['app_id']]
        )->id;
        return response()->json([
            'code'=>'200',
            'msg'=>'ok',
            'data'=>['openId'=>$data['openId'],'nickName'=>$data['nickName'],'avatarUrl'=>$data['avatarUrl'],'user_id'=>$id]
        ]);
    }
    /*
    public function bind_phone(Request $request){
        $encryptedData = $request->get('encryptedData','');
        $iv            = $request->get('iv','');
        $sessionKey    = $request->get('sessionKey','');
        $open_id       = $request->get('open_id','');

        $miniProgram = Factory::miniProgram($this->config);
        $data =$miniProgram->encryptor->decryptData($sessionKey, $iv, $encryptedData);
        if( WxUser::where('open_id',$open_id)->update(['mobile'=>$data->phoneNumber]) ){
            return ['code'=>200,'msg'=>'ok'];
        }

    }
    public function bind_contract_no(Request $request){
        $open_id    = $request->get('open_id','');
        $contract_no= $request->get('contract_no','');

        if( WxUser::where('open_id',$open_id)->update(['contract_no'=>$contract_no]) ){
            return ['code'=>200,'msg'=>'ok'];
        }
    }
    */
	//处理微信昵称表情符号
	private function removeEmoji($nickname) {
		$clean_text = "";
		// Match Emoticons
		$regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
		$clean_text = preg_replace($regexEmoticons, '', $nickname);

		// Match Miscellaneous Symbols and Pictographs
		$regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
		$clean_text = preg_replace($regexSymbols, '', $clean_text);

		// Match Transport And Map Symbols
		$regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
		$clean_text = preg_replace($regexTransport, '', $clean_text);

		// Match Miscellaneous Symbols
		$regexMisc = '/[\x{2600}-\x{26FF}]/u';
		$clean_text = preg_replace($regexMisc, '', $clean_text);

		// Match Dingbats
		$regexDingbats = '/[\x{2700}-\x{27BF}]/u';
		$clean_text = preg_replace($regexDingbats, '', $clean_text);

		return $clean_text;
	}
}
