<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\ImageUpload;
use Image;
use App\Exceptions\ImageUploadException;
use Storage;
use App\Repositories\Eloquent\ImageRepositoryEloquent;

class ImageUploadController extends Controller
{
    //
    protected $imageUpload;
    protected $imageRepositoryEloquent;

    public function __construct(ImageUpload $imageUpload, ImageRepositoryEloquent $imageRepositoryEloquent)
    {
        $this->imageUpload = $imageUpload;

        $this->imageRepositoryEloquent = $imageRepositoryEloquent;
    }

    public function uploadImage(Request $request){
		if(!empty($_FILES['file'])){
            $uploaddir = '/upload/';
            $uploadfile = $uploaddir . basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], public_path($uploadfile))) {
                return response()->json(['code'=>200,'msg'=>'ok','data'=>env('APP_URL','').$uploadfile]);
            }else{
                return response()->json(['code'=>1015,'msg'=>'error','data'=>[] ]);
            }
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
}
