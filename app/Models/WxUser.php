<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WxUser extends Model
{
    //
    protected $table='t_wx_user';
    public $timestamps = true;

    protected $fillable = [
        'open_id','contract_no','nick_name','avatar_url','city','country','country_code','mobile','gender','language','province','app_id'
    ];
}
