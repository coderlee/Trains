<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests\TrainPost;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\ImageRepositoryEloquent;
use App\Repositories\Eloquent\TrainsRepositoryEloquent;
use App\Models\Trains;
use App\Models\TrainCharge;
use Auth;
use App\Services\ImageUpload;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Response;
use Cache;

class TrainsController extends Controller
{
    protected $imageUpload;
    protected $imageRepositoryEloquent;
    protected $trainsRepositoryEloquent;

    public function __construct(TrainsRepositoryEloquent $trainsRepositoryEloquent,ImageRepositoryEloquent $imageRepositoryEloquent,ImageUpload $imageUpload)
    {
        $this->imageUpload = $imageUpload;
        $this->imageRepositoryEloquent = $imageRepositoryEloquent;
        $this->trainsRepositoryEloquent= $trainsRepositoryEloquent;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		//Cache::flush();
        $lists = Trains::where(function ($query) use ($request){
            if($request->has('title')){
                $query->where('title','like','%'.$request->get('title').'%');
            }
            if($request->has('apply_start')){
                $query->where('apply_start','>=',$request->get('apply_start'));
            }
            if($request->has('apply_end')){
                $query->where('apply_end','<=',$request->get('apply_end'));
            }
            if($request->has('train_start')){
                $query->where('train_start','>=',$request->get('train_start'));
            }
            if($request->has('train_end')){
                $query->where('train_end','<=',$request->get('train_end'));
            }
            if($request->has('status')){
                $query->where('status',$request->get('status'));
            }
        })->whereHas(
            'admin_user',function($query) use ($request){
                if($request->has('recorder')){
                    $query->where('name','like','%'.$request->get('recorder').'%');
                }
            }
        )
            ->orderBy('id','desc')
            ->paginate(10);
        return view('admin.trains.index',['lists'=>$lists]);
    }
    /**
     * 签到二维码
     */
    public function qrcode(Request $request){
        $train_id = $request->get('train_id','');
        $url = route('student.index',['id'=>$train_id]);
        $code = QrCode::format('png')->size(200)->color(66,66,66)->generate($url);
        return ['code'=>base64_encode($code)];
    }
    /**
     * 下载二维码
     */
    public function download($id){
        $url = route('student.index',['id'=>$id]);
        $filename = '../public/qrcodes/train_'.$id.'.png';
        QrCode::format('png')->size(400)->generate($url,$filename);
        return response()->download($filename);

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('admin.trains.create',['is_edit' => false]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TrainPost $request)
    {
        $train_info = $request;
        $train_info->recorder = Auth::user()->id;
        $train_info->status = 1;
        // 上传图片
        if ($request->hasFile('banner')) {
            $upload_status = $this->imageUpload->uploadImage($request->file('banner'));
            $file_arr = $upload_status['filename'];
            // 保存到图片表
            $insert_id = $this->saveImageInfo($file_arr);
            $train_info->banner = env('APP_URL','').'/'.$file_arr['small'];
        }
        $id = $this->trainsRepositoryEloquent->saveTrain($train_info);
        if ($id) {
            $charge_way = $request->get('charge_way',1);
            TrainCharge::create([
                'train_id'       =>$id,
                'charge_way'     =>$request->get('charge_way','1'),
                'unit'           =>$request->get('unit','1'),
                'min_num'        =>$request->get('min_num','0'),
                'max_nursery_num'=>$request->get('max_nursery_num','0'),
                'attr1_name'     =>$request->get('attr1_name_'.$charge_way,''),
                'attr1_value'    =>$request->get('attr1_value_'.$charge_way,''),
                'attr1_price'    =>$request->get('attr1_price_'.$charge_way,'0'),
                'attr2_name'     =>$request->get('attr2_name_'.$charge_way,''),
                'attr2_value'     =>$request->get('attr2_value_'.$charge_way,''),
                'attr2_price'     =>$request->get('attr2_price_'.$charge_way,'0'),
                'attr3_name'     =>$request->get('attr3_name_'.$charge_way,''),
                'attr3_value'     =>$request->get('attr3_value_'.$charge_way,''),
                'attr3_price'     =>$request->get('attr3_price_'.$charge_way,'0'),
				'is_card'         =>$request->get('is_card','0'),
                'is_health'       =>$request->get('is_health','0'),
                'is_labor'        =>$request->get('is_labor','0'),
                'is_learnership'  =>$request->get('is_learnership','0'),
            ]);
            return redirect()->route('trains.index');
        }
    }
    /**
     * 保存图片信息到数据库
     * @param $file_arr array
     * @return string 插入ID
     * */
    protected function saveImageInfo($file_arr)
    {
        $insert_id = $this->imageRepositoryEloquent->saveImage($file_arr, Auth::user());
        return $insert_id;
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
        $train = Trains::with('get_charge')->find($id);
        //dd($train->toArray());
        return view('admin.trains.create',['train'=>$train,'is_edit' => true]);
    }
    /**
     * 发布
     */
    public function change_status(Request $request){
        $id = $request->get('id','');
        if($id){
            $train = $this->trainsRepositoryEloquent->find($id);
            if($train){
                $this->trainsRepositoryEloquent->update(['status'=>2],$id);
                return ['code'=>200,'msg'=>'ok'];
            }
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TrainPost $request, $id)
    {
        //
        $train_info = $request;
        // 上传图片
        if ($request->hasFile('banner')) {
            $upload_status = $this->imageUpload->uploadImage($request->file('banner'));
            $file_arr = $upload_status['filename'];
            // 保存到图片表
            $insert_id = $this->saveImageInfo($file_arr);
            $train_info->banner = env('APP_URL','').'/'.$file_arr['small'];
        }
        $result = $this->trainsRepositoryEloquent->updateTrain($id, $train_info);
        if ($result) {
            TrainCharge::where("train_id",$id)->delete();
            $charge_way = $request->get('charge_way',1);
            TrainCharge::create([
                'train_id'       =>$id,
                'charge_way'     =>$request->get('charge_way','1'),
                'unit'           =>$request->get('unit','1'),
                'min_num'        =>$request->get('min_num','0'),
                'max_nursery_num'=>$request->get('max_nursery_num','0'),
                'attr1_name'     =>$request->get('attr1_name_'.$charge_way,''),
                'attr1_value'    =>$request->get('attr1_value_'.$charge_way,''),
                'attr1_price'    =>$request->get('attr1_price_'.$charge_way,'0'),
                'attr2_name'     =>$request->get('attr2_name_'.$charge_way,''),
                'attr2_value'     =>$request->get('attr2_value_'.$charge_way,''),
                'attr2_price'     =>$request->get('attr2_price_'.$charge_way,'0'),
                'attr3_name'     =>$request->get('attr3_name_'.$charge_way,''),
                'attr3_value'     =>$request->get('attr3_value_'.$charge_way,''),
                'attr3_price'     =>$request->get('attr3_price_'.$charge_way,'0'),
				'is_card'         =>$request->get('is_card','0'),
                'is_health'       =>$request->get('is_health','0'),
                'is_labor'        =>$request->get('is_labor','0'),
                'is_learnership'  =>$request->get('is_learnership','0'),
            ]);
            return redirect()->route('trains.index');
        }
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
