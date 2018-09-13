<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class NurseryStudents.
 *
 * @package namespace App\Models;
 */
class NurseryStudents extends Model implements Transformable
{
    use TransformableTrait;
    protected $table='t_nursery_students';
    public $timestamps=true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contract_no','apply_user','student_name','student_sex','student_phone','student_position',
        'idcard','card_z','card_f','health_1','health_2','health_3','labor_1','labor_2','learnership'
    ];
}
