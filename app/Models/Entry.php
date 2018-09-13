<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Entry.
 *
 * @package namespace App\Models;
 */
class Entry extends Model implements Transformable
{
    use TransformableTrait;
    protected $table='t_train_order';
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contract_no','park_name','apply_user_name','apply_phone','apply_num','apply_form','train_id','total_fee','is_paid','payment','status','from',
        'pay_time','remark','order_sn'
    ];
    public function get_train(){
        return $this->hasOne('App\Models\Trains','id','train_id');
    }

    public function get_user(){
        return $this->hasOne('App\Models\WxUser','id','apply_user');
    }
    public function get_students(){
        return $this->hasMany('App\Models\Students','order_id','id');
    }
}
