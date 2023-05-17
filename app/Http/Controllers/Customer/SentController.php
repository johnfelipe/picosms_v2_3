<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class SentController extends Controller
{
    public function index(){
        $data['messages']=auth('customer')->user()->sent_messages()->orderBy('created_at','desc')->paginate(10);
        return view('customer.smsbox.sent',$data);
    }
    public function move_trash(Request $request){
        $request->validate([
            'ids'=>'required'
        ]);
        $ids=explode(',', $request->ids);

        auth('customer')->user()->sent_messages()->whereIn('id',$ids)->delete();

        return back()->with('success', 'Message successfully moved to trash');

    }
}
