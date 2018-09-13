<?php

namespace App\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\EntryRepository;
use App\Models\Entry;
use App\Repositories\Validators\EntryValidator;

/**
 * Class EntryRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class EntryRepositoryEloquent extends BaseRepository implements EntryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Entry::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    public function saveOrder($data){
        $this->model->order_sn    = time().rand(111,333).$data->train_id;
        $this->model->contract_no = $data->contract_no;
        $this->model->park_name   = $data->park_name;
        $this->model->apply_user_name  = $data->student_name;
        $this->model->apply_phone = $data->student_phone;
        $this->model->apply_num   = 1;
        $this->model->apply_form  = 1;
        $this->model->train_id    = $data->train_id;
        $this->model->total_fee   = $data->total_fee;
        $this->model->is_paid     = 1;
        $this->model->payment     = $data->payment;
        $this->model->pay_time    = $data->pay_time;
        $this->model->status      = 0;
        $this->model->from        = 2;

        $this->model->save();
        return $this->model->id;
    }
    public function saveApiOrder($data){
        $this->model->order_sn    = time().rand(111,333).$data['train_id'];
        $this->model->contract_no = $data['contract_no'];
        $this->model->park_name   = $data['park_name'];
        $this->model->apply_user  = $data['apply_user'];
        $this->model->apply_user_name  = $data['apply_user_name'];
        $this->model->apply_phone = $data['mobile'];
        $this->model->apply_num   = $data['apply_num'];
        $this->model->apply_form  = $data['apply_form'];
        $this->model->train_id    = $data['train_id'];
        $this->model->total_fee   = $data['total_fee'];
        $this->model->is_paid     = $data['is_paid'];
        $this->model->payment     = 1;
        $this->model->status      = $data['status'];
        $this->model->from        = 1;
        $this->model->save();
        return $this->model->id;
    }
}
