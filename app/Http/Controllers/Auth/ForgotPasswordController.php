<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function show_form()
    {
        return view('auth.password_reset_form');
    }

    public function sent_email(Request $request)
    {
        $customer = Customer::where('email', $request->email)->first();
        if (!$customer) return back()->with('fail', 'Account not found with this email');
        $token = substr(md5(mt_rand()), 0, 30);

        $data = [
            'user_id' => $customer->id,
            'token' => $token
        ];
        DB::table('password_reset')->insert($data);

        //TODO::send email here with type like customer or admin
        //URL: password/reset?customer=1&token=alksjdflasjkdfl&type=customer
        $data = array('name'=>$customer->full_name,'url'=>route('password.reset.confirm',['customer'=>$customer->id,'token'=>$token,'type'=>'customer']));
        Mail::send('mail.password-reset', $data, function($message) use ($customer){
            $message->to($customer->email)->subject
            ('Forget password');
        });
        return redirect()->route('login')->with('success', 'An instruction has been sent to your email');
    }

    public function reset_form(Request $request)
    {

        $data['id'] = $id = $request->customer;
        $data['type'] = $type = $request->type;
        $data['token'] = $token = $request->token;
        $reset = DB::table('password_reset')->where(['user_id' => $id, 'token' => $token])->first();
        if (!$reset || !in_array($type, ['customer', 'user'])) return redirect()->route('login')->with('fail', 'Token is invalid or has been expired');

        if ($type == 'customer')
            $user = Customer::find($id);
        else
            $user = User::find($id);

        if (!$user) return redirect()->route('login')->with('fail', 'User has been removed or blocked');

        return view('auth.password_confirm_form', $data);


    }

    public function reset_confirm(Request $request)
    {
        $request->validate([
            'password'=>'required|min:6|confirmed',
        ]);
        $id = $request->customer;
        $type = $request->type;
        $token = $request->token;
        $reset = DB::table('password_reset')->where(['user_id' => $id, 'token' => $token])->first();
        if (!$reset || !in_array($type, ['customer', 'user'])) return redirect()->route('login')->with('fail', 'Token is invalid or has been expired');

        if ($type == 'customer')
            $user = Customer::find($id);
        else
            $user = User::find($id);

        if (!$user) return redirect()->route('login')->with('fail', 'User has been removed or blocked');

        $user->password=$request->password;
        $user->save();

        DB::table('password_reset')->where(['user_id' => $id])->delete();

        return redirect()->route('login')->with('success', 'Successfully reset your password');
    }
}
