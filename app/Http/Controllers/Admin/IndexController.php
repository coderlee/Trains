<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Cache;
use App\Models\Trains;
use App\Models\Students;
use App\Models\Entry;

class IndexController extends Controller
{
    //
    public function index()
    {
        Cache::flush();
        $train_count  = Trains::where('status',2)->count();
        $student_count= Students::where('status',4)->where('is_paid',1)->count();
        $order_total  = Entry::where('status',7)->where('is_paid',1)->count();
        return view('admin.index',['train_count'=>$train_count,'student_count'=>$student_count,'order_total'=>$order_total]);
    }

    public function icon()
    {
        return view('admin.icon');
    }
}
