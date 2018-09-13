<?php

namespace App\Repositories\Eloquent;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Contracts\NurseryStudentsRepository;
use App\Models\NurseryStudents;
use App\Repositories\Validators\NurseryStudentsValidator;

/**
 * Class NurseryStudentsRepositoryEloquent.
 *
 * @package namespace App\Repositories\Eloquent;
 */
class NurseryStudentsRepositoryEloquent extends BaseRepository implements NurseryStudentsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NurseryStudents::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    public function saveStudents($data){
        $this->model->apply_user =$data->apply_user;
        $this->model->contract_no =$data->contract_no;
        $this->model->student_name =$data->student_name;
        $this->model->student_sex =$data->student_sex;
        $this->model->student_phone =$data->student_phone;
        $this->model->student_position =$data->student_position;
        $this->model->school   =$data->school;
        $this->model->education=$data->education;
        $this->model->profession=$data->profession;
        $this->model->idcard   =$data->idcard;
        $this->model->card_z   =$data->card_z;
        $this->model->card_f   =$data->card_f;
        $this->model->health_1 =$data->health_1;
        $this->model->health_2 =$data->health_2;
        $this->model->health_3 =$data->health_3;
        $this->model->labor_1  =$data->labor_1;
        $this->model->labor_2  =$data->labor_2;
        $this->model->learnership  =$data->learnership;
        $this->model->save();
        return $this->model->id;
    }
}
