<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function index()
    {

        $data['admin'] = auth()->user();
        return view('admin.settings.index', $data);
    }

    public function profile_update(Request $request)
    {
        $request->validate([
            'u_name' => 'required',
            'email' => 'required|unique:users,email,' . auth()->id(),
            'profile' => 'image',
        ]);
        $pre_email = auth()->user()->email;
        $new_email = $request->email;
        $user = auth()->user();
        if ($pre_email != $new_email) {
            $user->email_verified_at = null;

            //TODO::send email here to verify email address
        }
        $user->name = $request->u_name;
        $user->email = $new_email;
        if ($request->password)
            $user->password = bcrypt($request->password);

        if ($request->hasFile('profile')) {
            $file = $request->file('profile');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/uploads'), $imageName);
            $user->profile_picture = $imageName;
        }

        $user->save();
        cache()->flush();
        return redirect()->back()->with('success', 'Profile successfully updated');
    }

    public function app_update(Request $request)
    {
        $request->validate([
            'app_name' => 'required',
            'logo'=>'image',
            'favicon'=>'image'
        ]);

        //TODO:: in future update the settings dynamically

        //update application name
        $data = ['name' => 'app_name'];
        $setting = auth()->user()->settings()->firstOrNew($data);
        $setting->value = $request->app_name;
        $setting->save();

        //update favicon
        if ($request->hasFile('favicon')) {

            $file = $request->file('favicon');
            $favicon_name = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/uploads'), $favicon_name);

            $data = ['name' => 'app_favicon'];
            $setting = auth()->user()->settings()->firstOrNew($data);
            $setting->value = $favicon_name;
            $setting->save();
        }

        //update logo
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $logo_name = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('/uploads'), $logo_name);

            $data = ['name' => 'app_logo'];
            $setting = auth()->user()->settings()->firstOrNew($data);
            $setting->value = $logo_name;
            $setting->save();
        }
        cache()->flush();
        return redirect()->back()->with('success', 'Application successfully updated');
    }

    public function smtp_update(Request $request)
    {
        $request->validate([
           'from'=>'required|email',
           'host'=>'required',
           'name'=>'required',
           'username'=>'required',
           'password'=>'required',
           'port'=>'required|numeric',
           'encryption'=>'required|in:ssl,tls',
        ]);
        unset($request['_token']);


        $from = "Picotech Support <demo@picotech.app>";
        $to = "Picotech Support <demo@picotech.app>";
        $subject = "Hi!";
        $body = "Hi,\n\nHow are you?";

        $host = $request->host;
        $port = $request->port;
        $username = $request->username;
        $password = $request->password;
        $config = array(
            'driver' => 'smtp',
            'host' => $host,
            'port' => $port,
            'from' => array('address' => $request->from, 'name' => $request->name),
            'encryption' => $request->encryption,
            'username' => $username,
            'password' => $password,
        );
        Config::set('mail', $config);

        try {
            Mail::send('sendMail', ['htmlData' => $body], function ($message) {
                $message->to("tuhin.picotech@gmail.com")->subject
                ("Setting check");
            });
        } catch (\Exception $ex) {
            dd($ex->getMessage());
            return redirect()->back()->withErrors(['msg' => trans('Invalid email credentials')]);
        }


        foreach ($request->all() as $key => $req) {
            $data = ['name' => 'mail_' . $key];
            $setting = auth()->user()->settings()->firstOrNew($data);
            $setting->value = $request->$key;
            $setting->save();
        }
        //we need to flush the cache as settings are from cache
        cache()->flush();

        return back()->with('success', 'SMTP configuration successfully updated');
    }

    public function api_update(Request $request)
    {
        $type = $request->gateway;

        if ($type == 'signalwire') {
            $project_id = $request->sw_project_id;
            $sw_space_url = $request->sw_space_url;
            $sw_token = $request->sw_token;

            $dataArray = [
                'sw_project_id' => $project_id,
                'sw_space_url' => $sw_space_url,
                'sw_token' => $sw_token,
            ];

            $data = ['name' => 'signalwire'];
            $setting = auth()->user()->settings()->firstOrNew($data);
            $setting->value = json_encode($dataArray);
            $setting->save();
        }
        else if ($type == 'twilio') {

            $tw_sid = $request->tw_sid;
            $tw_token = $request->tw_token;
            $dataArray = [
                'tw_sid' => $tw_sid,
                'tw_token' => $tw_token
            ];
            $data = ['name' => 'twilio'];
            $setting = auth()->user()->settings()->firstOrNew($data);
            $setting->value = json_encode($dataArray);
            $setting->save();
        }
        else if ($type == 'nexmo') {

            $nx_api_key = $request->nx_api_key;
            $nx_api_secret = $request->nx_api_secret;
            $dataArray = [
                'nx_api_key' => $nx_api_key,
                'nx_api_secret' => $nx_api_secret
            ];

            $data = ['name' => 'nexmo'];
            $setting = auth()->user()->settings()->firstOrNew($data);
            $setting->value = json_encode($dataArray);
            $setting->save();
        }
        else if ($type == 'telnyx') {

            $tl_api_key = $request->tl_api_key;

            $dataArray = [
                'tl_api_key' => $tl_api_key,
            ];

            $data = ['name' => 'telnyx'];
            $setting = auth()->user()->settings()->firstOrNew($data);
            $setting->value = json_encode($dataArray);
            $setting->save();
        }
        else if ($type == 'plivo') {

            $pl_auth_id = $request->pl_auth_id;
            $pl_auth_token = $request->pl_auth_token;

            $dataArray = [
                'pl_auth_id' =>$pl_auth_id,
                'pl_auth_token' =>$pl_auth_token,
            ];

            $data = ['name' => 'plivo'];
            $setting = auth()->user()->settings()->firstOrNew($data);
            $setting->value = json_encode($dataArray);
            $setting->save();
        }

        cache()->flush();
        return response()->json(['status'=>'success','message'=>'API successfully updated']);
    }

}
