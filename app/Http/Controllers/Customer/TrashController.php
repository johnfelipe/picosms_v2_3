<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index(){
        $data['trashes']=auth('customer')->user()->messages()->onlyTrashed()->get();
        return view('customer.smsbox.trash',$data);
    }
}
