<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundOrder extends Model
{
    protected $table = 't_refund_log';

    public $timestamps = true;

    protected $fillable =[
        'order_sn','transaction_id','total_fee','refund_no','refund_id','refund_fee','refund_desc','is_refund'
    ];
}
