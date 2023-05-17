<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Number;
use Illuminate\Http\Request;
use Plivo\RestClient;
use SignalWire\Rest\Client;
use Vonage\Client\Credentials\Basic;

class ScheduleController extends Controller
{
    public function process(){

        $messages=Message::where(['type'=>'sent','schedule_completed'=>'no'])
            ->whereNotNull('schedule_datetime')
            ->where('schedule_datetime','<',now())
            ->get();


        foreach ($messages as $message){
            $current_plan = $message->user->plan;
            if (!$current_plan)
                return response()->json(['message'=>'Plan not found']);

            //subtracting one sms TODO:: will need to count text and sub that
            $pre_available_sms = $current_plan->available_sms;
            $new_available_sms = $pre_available_sms -1;

            //if not enough sms then return
            if ($new_available_sms < 0)
                return response()->json(['message'=>'Don\'t have enough message']);

            $numbersJson=json_decode($message->numbers);

            $number = Number::where('number', $numbersJson->from)->first();
            if (!$number)
                return response()->json(['message'=>'Number not found please contact with administrator']);

                $gateway = $number->from;
                if ($gateway == 'signalwire') {
                    $credentials = json_decode(get_settings('signalwire'));

                    //TODO:: check credentials availability
                    if (!$credentials->sw_project_id)
                        return response()->json(['message'=>'Credentials not found. Please contact with the administrator']);

                    $client = new Client($credentials->sw_project_id, $credentials->sw_token, array("signalwireSpaceUrl" => $credentials->sw_space_url));
                    foreach ($numbersJson->to as $to) {
                        $message = $client->messages
                            ->create($to,
                                array("from" => $number->number, "body" => $message->body)
                            );
                    }
                }
                else if ($gateway == 'twilio') {
                    $credentials = json_decode(get_settings('twilio'));
                    if (!$credentials->tw_sid)
                        return response()->json(['message'=>'Credentials not found. Please contact with the administrator']);

                    $sid = $credentials->tw_sid;
                    $token = $credentials->tw_token;
                    $client = new \Twilio\Rest\Client($sid, $token);
                    foreach ($numbersJson->to as $to) {
                        $client->messages->create(
                            $to,
                            [
                                'from' => $number->number,
                                'body' => $message->body
                            ]
                        );
                    }
                }
                else if ($gateway == 'nexmo') {
                    $credentials = json_decode(get_settings('nexmo'));
                    if (!$credentials->tw_sid)
                        return response()->json(['message'=>'Credentials not found. Please contact with the administrator']);

                    $api_key = $credentials->nx_api_key;
                    $api_secret = $credentials->nx_api_secret;
                    $client = new \Vonage\Client(new Basic($api_key, $api_secret));
                    foreach ($numbersJson->to as $to) {
                        $message = $client->message()->send([
                            'to' => $to,
                            'from' => $number->number,
                            'text' => $message->body
                        ]);
                    }

                }
                else if($gateway=='telnyx'){
                    $credentials = json_decode(get_settings('telnyx'));
                    if (!$credentials->tl_api_key)
                        return response()->json(['message'=>'Credentials not found. Please contact with the administrator']);

                    \Telnyx\Telnyx::setApiKey($credentials->tl_api_key);
                    foreach ($numbersJson->to as $to) {
                        \Telnyx\Message::Create(['from' => $number->number, 'to' => $to, 'text' => $message->body]);
                    }

                }
                else if($gateway=='plivo'){
                    $credentials = json_decode(get_settings('plivo'));
                    if (!$credentials->pl_auth_id || !$credentials->pl_auth_token)
                        return response()->json(['message'=>'Credentials not found. Please contact with the administrator']);

                    $client = new RestClient($credentials->pl_auth_id, $credentials->pl_auth_token);
                    $message_created = $client->messages->create(
                        $number->number,
                        $numbersJson->to,
                        $message->body
                    );

                }

            $message->update(['schedule_completed'=>'yes']);
            }
    }
}
