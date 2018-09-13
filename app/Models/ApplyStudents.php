<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplyStudents extends Model
{
    //
    protected $table='t_apply_students';
    public $timestamps = true;
    protected $fillable=[
        'student_id','contract_no','train_id','apply_user'
    ];

    public function get_student(){
        return $this->hasOne('App\Models\NurseryStudents','id','student_id');
    }
}
