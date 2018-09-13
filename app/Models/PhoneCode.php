<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneCode extends Model
{
    //
    protected $table='t_phone_code';

    public $timestamps=false;

    protected $fillable=[
        'phone','code','send_time','dead_time','status','next_time'
    ];
}
