<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayInfo extends Model
{
    //
    protected $table = 't_pay_info';
    public $timestamps='true';

    protected $fillable=[
        'order_sn','trade_no','total_fee','pay_time','openid'
    ];
}
