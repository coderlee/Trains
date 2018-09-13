<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainCharge extends Model
{
    //
    protected $table='t_train_charge';
    public $timestamps=true;
    protected $fillable=[
        'train_id','charge_way','unit','max_nursery_num','min_num',
        'attr1_name','attr1_value','attr1_price',
        'attr2_name','attr2_value','attr2_price',
        'attr3_name','attr3_value','attr3_price',
		'is_card','is_health','is_labor','is_learnership',
    ];
}
