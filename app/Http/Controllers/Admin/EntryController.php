<?php

namespace App\Http\Controllers\Admin;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Trains;
use App\Models\PayInfo;
use App\Models\NurseryStudents;
use App\Http\Requests\EntryPost;
use App\Repositories\Eloquent\EntryRepositoryEloquent;
use App\Repositories\Eloquent\StudentsRepositoryEloquent;
use App\Repositories\Eloquent\NurseryStudentsRepositoryEloquent;
use DB;
use Exception;
use EasyWeChat\Factory;
use Log;
use App\Models\RefundOrder;
use App\Models\OrderStudents;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EntryController extends Controller
{
    protected $entryRepositoryEloquent;
    protected $studentsRepositoryEloquent;
    protected $nurseryStudentsRepositoryEloquent;
	private $config;

    public function __construct(EntryRepositoryEloquent $entryRepositoryEloquent,StudentsRepositoryEloquent $studentsRepositoryEloquent,NurseryStudentsRepositoryEloquent $nurseryStudentsRepositoryEloquent)
    {
        $this->entryRepositoryEloquent   =$entryRepositoryEloquent;
        $this->studentsRepositoryEloquent=$studentsRepositoryEloquent;
        $this->nurseryStudentsRepositoryEloquent=$nurseryStudentsRepositoryEloquent;
		$this->config=config('wechat.mini');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //
        $lists = Entry::where(function($query) use ($request){
            if($request->has('contract_no')){
                $query->where('contract_no',$request->get('contract_no'));
            }
            if($request->has('park_name')){
                $query->where('park_name',"%".$request->get('park_name')."%");
            }
            if($request->has('apply_phone')){
                $query->where('apply_phone',$request->get('apply_phone'));
            }
            if($request->has('from')){
                $query->where('from',$request->get('from'));
            }
            if($request->get('train_id')){
                $query->where('train_id',$request->get('train_id'));
            }
            if($request->get('is_paid') || $request->get('is_paid') ==='0'){
                $query->where('is_paid',$request->get('is_paid'));
            }
        })->with(['get_train'])
            ->orderBy('created_at','desc')
            ->paginate(10);
		foreach($lists as $key=>$val){
			if($val->is_paid==1){
				$trade_no = PayInfo::where('order_sn',$val->order_sn)->value('trade_no');
				$lists[$key]->trade_no = $trade_no;
			}
		}
        $trains = Trains::where('status',2)->get();
        return view('admin.entry.index',['lists'=>$lists,'trains'=>$trains,'search'=>$request->toArray()]);
    }
    /**
     * 园所验证
     */
    public function check_nursery(Request $request){
        $contract_no = $request->get('contract_no','');
        if($contract_no){
            $data =[];
            $data['key'] = '27a15511082d11e6b23b00163e005ebf';
            $data['dt']  = 'json';
            $data['code']= $contract_no;

            $http = new Client();
            $response = $http->request('post','http://base.rybbaby.com/api/base/schoolInfo',['form_params'=>$data]);
            $data = json_decode((string)$response->getBody(), true);
            if($data['result']){
                return $data['json'];
            }else{
                return [];
            }
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
        $trains = Trains::where('status','2')->orderBy('id','desc')->get();
        return view('admin.entry.create',['trains'=>$trains]);
    }
    /**
     * 导出
     */
    public function export_data(Request $request){
        $lists = Entry::where(function($query) use ($request){
            if($request->has('contract_no')){
                $query->where('contract_no',$request->get('contract_no'));
            }
            if($request->has('park_name')){
                $query->where('park_name',"%".$request->get('park_name')."%");
            }
            if($request->has('apply_phone')){
                $query->where('apply_phone',$request->get('apply_phone'));
            }
            if($request->has('from')){
                $query->where('from',$request->get('from'));
            }
            if($request->get('train_id')){
                $query->where('train_id',$request->get('train_id'));
            }
            if($request->get('is_paid')){
                $query->where('is_paid',$request->get('is_paid'));
            }
        })->with(['get_train'])
            ->orderBy('created_at','desc')
            ->get();
        foreach($lists as $key=>$val){
            if($val->is_paid==1){
                $trade_no = PayInfo::where('order_sn',$val->order_sn)->value('trade_no');
                $lists[$key]->trade_no = $trade_no;
            }
        }
        $spreadsheet = new Spreadsheet();
        $worksheet = $spreadsheet->getActiveSheet();
        $worksheet->setTitle('培训报名表');

        $worksheet->setCellValueByColumnAndRow(1, 1, '园所合同号');
        $worksheet->setCellValueByColumnAndRow(2, 1, '园所名称');
        $worksheet->setCellValueByColumnAndRow(3, 1, '培训主题');
        $worksheet->setCellValueByColumnAndRow(4, 1, '报名手机号');
        $worksheet->setCellValueByColumnAndRow(5, 1, '报名人数');
        $worksheet->setCellValueByColumnAndRow(6, 1, '是否支付');
        $worksheet->setCellValueByColumnAndRow(7, 1, '支付费用');
        $worksheet->setCellValueByColumnAndRow(8, 1, '支付时间');
        $worksheet->setCellValueByColumnAndRow(9, 1, '交易号');

        for($i=0;$i<count($lists);$i++){
            $j =$i+2;
            $worksheet->setCellValueByColumnAndRow(1,$j,$lists[$i]->contract_no);
            $worksheet->setCellValueByColumnAndRow(2,$j,$lists[$i]->park_name);
            $worksheet->setCellValueByColumnAndRow(3,$j,$lists[$i]->get_train->title);
            $worksheet->setCellValueByColumnAndRow(4,$j,$lists[$i]->apply_phone);
            $worksheet->setCellValueByColumnAndRow(5,$j,$lists[$i]->apply_num);
            $worksheet->setCellValueByColumnAndRow(6,$j,$lists[$i]->is_paid==1?'已支付':'未支付');
            $worksheet->setCellValueByColumnAndRow(7,$j,$lists[$i]->total_fee);
            $worksheet->setCellValueByColumnAndRow(8,$j,$lists[$i]->pay_time);
            $worksheet->setCellValueByColumnAndRow(9,$j,$lists[$i]->trade_no );
        }
        $filename = '培训报名表.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        echo $writer->save('php://output');die();

    }
    /**
     * 退款
     */
    public function refund(Request $request){
        $id = $request->get('id','');
        $remark = $request->get('remark','');
        if($id){
            $entry = $this->entryRepositoryEloquent->find($id);
            $pay_info = PayInfo::where('order_sn',$entry->order_sn)->first();
            //微信支付
            $miniProgram = Factory::payment($this->config);
            $refund_no = time().rand(111,333).$entry->train_id;
            $result = $miniProgram->refund->byOutTradeNumber($entry->order_sn,$refund_no,$entry->total_fee*100,$entry->total_fee*100,[
                'refund_desc'=>$remark
            ]);
            Log::error('return refund info: '.json_encode($result));
            try{
                DB::beginTransaction();
                if($result['return_code'] =='SUCCESS'){
                    if(isset($result['result_code']) && $result['result_code'] == 'SUCCESS'){
                        RefundOrder::create([
                            'order_sn'       =>$pay_info->order_sn,
                            'transaction_id' =>$pay_info->trade_no,
                            'total_fee'      =>$pay_info->total_fee,
                            'refund_no'      =>$refund_no,
                            'refund_id'      =>$result['refund_id'],
                            'refund_fee'     =>$result['refund_fee']/100,
                            'refund_desc'    =>$remark,
                            'is_refund'      =>'1',
                        ]);
                    }else{
                        RefundOrder::create([
                            'order_sn'       =>$pay_info->order_sn,
                            'transaction_id' =>$pay_info->trade_no,
                            'total_fee'      =>$pay_info->total_fee,
                            'refund_no'      =>$refund_no,
                            'refund_id'      =>'0',
                            'refund_fee'     =>'0',
                            'refund_desc'    =>$remark,
                            'is_refund'      =>'0',
                        ]);
                    }
                    //修改订单状态
                    $entry->status =1;
                    $entry->save();

                    OrderStudents::where('order_id',$entry->id)->update(['status'=>-1]);
					//返还库存 减销量
					if($entry->from ==1){
						Trains::where('id',$entry->train_id)->increment('pre_num');
						Trains::where('id',$entry->train_id)->decrement('sale_num');
					}
					DB::commit();
                }
				return ['code'=>200,'msg'=>'退款申请已提交!'];
            }catch (Exception $e){
                DB::rollBack();
                return response()->json($e->getMessage());
            }

        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EntryPost $request)
    {
        $data = $request;
        $studnet_info =NurseryStudents::where('student_phone',$data->input('student_phone'))->first();
        if(!$studnet_info){
            $student_id = $this->nurseryStudentsRepositoryEloquent->saveStudents($data);
            $data->student_id = $student_id;
        }else{
            $data->student_id = $studnet_info['id'];
        }
        $train_info = Trains::where('id',$data->train_id)->first();
        if(!$train_info['pre_num']){
            return redirect()->route('entry.index')->withErrors('报名培训人数已满')->withInput();
        }
        $order_id = $this->entryRepositoryEloquent->saveOrder($data);
        $data->order_id = $order_id;
        $order_students = $this->studentsRepositoryEloquent->saveOrderStudent($data);
        if($order_students){
            //减库存,加销量
            Trains::where('id',$data->train_id)->decrement('pre_num');
            Trains::where('id',$data->train_id)->increment('sale_num');
            return redirect()->route('entry.index');
        }
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
