<?php

namespace App\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\StudentsRepository;
use App\Models\Students;
use App\Repositories\Validators\StudentsValidator;

/**
 * Class StudentsRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class StudentsRepositoryEloquent extends BaseRepository implements StudentsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Students::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function saveOrderStudent($data){
        $this->model->order_id   = $data->order_id;
        $this->model->student_id = $data->student_id;
        $this->model->fee        = $data->total_fee;
        $this->model->is_paid    = 1;
        $this->model->status     = $data->student_status;
        if($data->student_status =='3'){
            $this->model->sign_time=date("Y-m-d H:i:s");
        }
        $this->model->save();
        return $this->model->id;
    }
    public function saveApiOrderStudent($data){
        $this->model->order_id   = $data['order_id'];
        $this->model->student_id = $data['student_id'];
        $this->model->fee        = $data['fee'];
        $this->model->is_paid    = $data['is_paid'];
        $this->model->status     = $data['status'];
        $this->model->save();
    }
}
