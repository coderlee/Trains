<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStudents extends Model
{
    //
    protected $table='t_order_students';
    public $timestamps = true;
    protected $fillable=[
        'order_id','student_id','fee','status'
    ];
}
