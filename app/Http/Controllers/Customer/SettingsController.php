<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $data['customer']=$customer = auth('customer')->user();
        $settings=$customer->settings;
        $customer_settings=[];
        foreach ($settings as $setting){
            $customer_settings[$setting->name]=$setting->value;
        }
        $data['customer_settings']=$customer_settings;
        return view('customer.settings.index', $data);
    }

    public function profile_update(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'email' => 'required|unique:customers,email,' . auth('customer')->id(),
            'profile'=>'image'
        ]);

        $user = auth('customer')->user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;

        if ($request->hasFile('profile')){
            $file=$request->file('profile');
            $imageName = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('/uploads'), $imageName);
            $user->profile_picture=$imageName;
        }
        $user->save();
        return redirect()->back()->with('success', 'Profile successfully updated');
    }

    public function password_update(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);
        $customer = auth('customer')->user();

        if (!Hash::check($request->old_password, $customer->password)) {
            return back()->with('fail', 'Invalid old password. Please try with valid password');
        }

        $customer->password = bcrypt($request->new_password); //remove the bcrypt
        $customer->save();

        return redirect()->back()->with('success', 'Password successfully changed');

    }

    public function notification_update(Request $request)
    {
        $request->validate([
            'isChecked' => 'required|in:true,false'
        ]);
        $data = [
            'name' => 'email_notification',
        ];

        $setting = auth('customer')->user()->settings()->firstOrNew($data);
        $setting->value = $request->isChecked;
        $setting->save();

        return response()->json(['status' => 'success', 'message' => 'Email notification updated']);
    }

    public function downloadSample($type,Request $request){
        if($type=='group'){
            return response()->download(public_path('csv/sample-group.csv'));
        }
    }

}
