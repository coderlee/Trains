<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Trains.
 *
 * @package namespace App\Models;
 */
class Trains extends Model implements Transformable
{
    use TransformableTrait;
    protected $table='t_trains';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable =[
        'title','banner','pre_num','jia_sale_num','train_start','train_end','train_adress','apply_start','apply_end','desc','desc_md','is_free','status','sort','shengming','recorder'
    ];
    protected $hidden=[
        
    ];
    public $timestamps = true;
    public function admin_user(){
        return $this->hasOne('App\Models\User','id','recorder');
    }
    public function get_charge(){
        return $this->hasOne('App\Models\TrainCharge','train_id','id');
    }
}

