<?php

namespace App\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\TrainsRepository;
use App\Models\Trains;
use App\Repositories\Validators\TrainsValidator;

/**
 * Class TrainsRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class TrainsRepositoryEloquent extends BaseRepository implements TrainsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Trains::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function saveTrain($train_info){
        $this->model->title = $train_info->title;
        $this->model->banner = $train_info->banner;
        $this->model->pre_num = $train_info->pre_num;
        $this->model->jia_sale_num = $train_info->jia_sale_num?$train_info->jia_sale_num:'0';
        $this->model->train_start = $train_info->train_start;
        $this->model->train_end = $train_info->train_end;
        $this->model->train_adress = $train_info->train_adress;
        $this->model->apply_start = $train_info->apply_start;
        $this->model->apply_end = $train_info->apply_end;
        $this->model->desc = $train_info->input('editormd-html-code');
        $this->model->desc_md = $train_info->input('editormd-markdown-doc');
        $this->model->is_free = $train_info->is_free;
        $this->model->status = $train_info->status;
        $this->model->sort = $train_info->sort;
        $this->model->shengming = $train_info->shengming;
        $this->model->recorder = $train_info->recorder;
        $this->model->save();
        return $this->model->id;
    }
    public function updateTrain($id, $train_info)
    {
        $train = $this->find($id);
        $train->title      = $train_info->title;
        $train->pre_num    = $train_info->pre_num;
        $train->jia_sale_num= $train_info->jia_sale_num;
        $train->train_start= $train_info->train_start;
        $train->train_end  = $train_info->train_end;
        $train->train_adress= $train_info->train_adress;
        $train->apply_start= $train_info->apply_start;
        $train->apply_end  = $train_info->apply_end;
        $train->desc       = $train_info->input('editormd-html-code');
        $train->desc_md    = $train_info->input('editormd-markdown-doc');
        $train->is_free    = $train_info->is_free;
        $train->sort       = $train_info->sort;
        $train->shengming       = $train_info->shengming;
        if (!empty($train_info->banner)) {
            $train->banner = $train_info->banner;
        }
        $train->save();
        return $train->id;
    }
}
