<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\User;
use App\Models\VerifyCustomer;
use foo\bar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CustomerLoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials['email'] = trim($request->email);
        $credentials['password'] = trim($request->password);
        $credentials['status'] = 'active';

        $customer = Customer::where(['email' => $credentials['email']])->first();

        if (isset($customer) && \Hash::check($credentials['password'], $customer->password)) {
            if(!$customer->email_verified_at) return back()->withErrors(['msg'=>'Please verify your email address.']);

            if ($customer && $customer->status != 'Active') return back()->withErrors(['msg' => 'Account temporary blocked. Contact with administrator']);

        }


        $remember_me = $request->has('remember_me') ? true : false;
        if (Auth::guard('customer')->attempt($credentials, $remember_me)) {
            return redirect()->route('customer.dashboard');
        }
        return back()->withErrors(['msg' => 'Invalid email or password. Please try again.']);
    }

    public function logout()
    {
        auth('customer')->logout();
        return redirect()->route('login');
    }

    public function sign_up()
    {
        return view('auth.registration');
    }

    public function sign_up_create(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:customers',
            'password' => 'required|min:6',
        ]);
        $admin = User::first();
        $request['admin_id'] = $admin->id;
        $request['status'] = 'inactive';
        $customer = $admin->customers()->create($request->all());

        //Assigning plan to customer
        $plan = Plan::first();
        $customer->plan()->create(['plan_id' => $plan->id, 'sms_limit' => $plan->sms_limit, 'available_sms' => $plan->sms_limit, 'price' => $plan->price]);

        //TODO:: sent a mail here for confirmation mail

        $token = Str::random(32);
        $verify = new VerifyCustomer();
        $verify->customer_id = $customer->id;
        $verify->token = $token;
        $verify->save();


        $data = array('name' => $customer->full_name, 'url' => route('customer.verify', ['customer' => $customer->id, 'token' => $token]));
        Mail::send('mail.verify_customer', $data, function ($message) use ($customer) {
            $message->to($customer->email)->subject
            ('Congratulations. Your account has been created');
        });
        return redirect()->route('login')->with('success', 'Congratulations !! An email has been sent to your mail address');

    }

    public function verify(Request $request)
    {
        $customer = $request->customer;
        $token = $request->token;

        $customer = Customer::find($customer);

        if (!$customer) return redirect()->route('login')->with('fail', 'Invalid token or token has been expired');

        $verify = VerifyCustomer::where(['customer_id' => $customer->id, 'token' => $token, 'status' => 'pending'])->first();

        if (!$verify) return redirect()->route('login')->with('fail', 'Invalid token or token has been expired.');

        $customer->status = 'active';
        $customer->email_verified_at = now();
        $customer->save();

        $verify->delete();

        return redirect()->route('login')->with('success', 'Email successfully verified');
    }

}
