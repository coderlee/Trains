<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Students.
 *
 * @package namespace App\Models;
 */
class Students extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table='t_order_students';
    protected $fillable = [
        'order_id','student_id','fee','is_paid','status','sign_time'
    ];

    public function get_order(){
        return $this->hasOne('App\Models\Entry','id','order_id');
    }
    public function get_nursery_user(){
        return $this->hasOne('App\Models\NurseryStudents','id','student_id');
    }
    public function get_train(){
        return $this->hasOne('App\Models\Trains','id','train_id');
    }
}
