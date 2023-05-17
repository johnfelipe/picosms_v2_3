<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Group;
use App\Models\Number;
use App\Models\SentFail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Textlocal;
use Vonage\Client\Credentials\Basic;
use Plivo\RestClient;
use SignalWire\Rest\Client;


class ComposeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->guard('customer')->user();
        $data['draft'] = $user->drafts()->where('id', $request->draft)->first();

        $usersToGroups = [];
        $usersToContacts = [];
        foreach ($user->active_groups as $group) {
            $usersToGroups[] = ['value' => $group->name, 'id' => $group->id, 'type' => 'group'];
        }
        foreach ($user->contacts as $contact) {
            $usersToContacts[] = ['value' => isset($contact->first_name) ? $contact->number . ' (' . $contact->first_name . ' ' . $contact->last_name . ')' : $contact->number, 'id' => $contact->id, 'type' => 'contact'];
        }


        $data['users_to_contacts'] = $usersToContacts;
        $data['users_to_groups'] = $usersToGroups;


        return view('customer.smsbox.compose', $data);
    }

    public function sentCompose(Request $request)
    {
        //  dd($request->all());
        $request->validate([
            'from_number' => 'required',
            'to_numbers' => 'required|array',
            'body' => 'required',
        ]);

        $messageFiles = [];
        $sendFailed = [];
        if ($request->mms_files) {

            foreach ($request->mms_files as $key => $file) {
                $messageFiles[] = $fileName = time() . $key . '.' . $file->extension();
                $file->move(public_path('uploads/'), $fileName);
            }
            $request['message_files'] = json_encode($messageFiles);
        }

        if (isset($request->isSchedule)) {
            $sd = Carbon::createFromTimeString($request->schedule);
            $request['schedule_datetime'] = $sd;
        }
        $allToNumbers = [];
        $allGroupIds = [];
        $allContactIds = [];

        foreach ($request->to_numbers as $item) {
            $number = (array)json_decode($item);
            if (isset($number['type']) && isset($number['id'])) {
                if ($number['type'] == 'contact') {
                    $allContactIds[] = $number['id'];
                } elseif ($number['type'] == 'group') {
                    $allGroupIds[] = $number['id'];
                }
            } else {
                $allToNumbers[] = $item;
            }
        }

        $contactNumbers = Contact::select('id', 'number')->whereIn('id', $allContactIds)->get();
        $groupNumbers = ContactGroup::with('contact')->whereIn('group_id', $allGroupIds)->get();

        foreach ($contactNumbers as $cn) {
            $allToNumbers[] = trim($cn->number);
        }
        foreach ($groupNumbers as $gn) {
            $allToNumbers[] = trim($gn->contact->number);
        }

        $allToNumbers = array_unique($allToNumbers);

        $request['to_numbers'] = $allToNumbers;
        $request['numbers'] = json_encode(['from' => $request->from_number, 'to' => $allToNumbers]);
        $request['type'] = 'sent';

        $current_plan = auth('customer')->user()->plan;
        if (!$current_plan)
            return back()->with('fail', 'Customer doesn\'t have any plan right now');

        //subtracting one sms TODO:: will need to count text and sub that
        $pre_available_sms = $current_plan->available_sms;
        $new_available_sms = $pre_available_sms - count($allToNumbers);

        //if not enough sms then return
        if ($new_available_sms < 0)
            return redirect()->back()->with('fail', 'Doesn\'t have enough sms');

        //send sms here using API
        $number = Number::where('number', $request->from_number)->first();
        if (!$number)
            return back()->with('fail', 'Number not found please contact with administrator');

        DB::beginTransaction();
        try {
            $gateway = $number->from;
            $newMessage = auth('customer')->user()->messages()->create($request->all());
            if (!isset($request->isSchedule)) {
                if ($gateway == 'signalwire') {
                    $credentials = json_decode(get_settings('signalwire'));

                    //TODO:: check credentials availability
                    if (!$credentials->sw_project_id)
                        return back()->with('fail', 'Credentials not found. Please contact with the administrator');

                    $client = new Client($credentials->sw_project_id, $credentials->sw_token, array("signalwireSpaceUrl" => $credentials->sw_space_url));
                    foreach ($request->to_numbers as $to) {
                        try {
                            if ($messageFiles) {
                                $newMessageFiles = $messageFiles;
                                array_walk($newMessageFiles, function (&$value, $index) {
                                    $value = asset('uploads/' . $value);
                                });

                                $message = $client->messages
                                    ->create($to,
                                        array("from" => $number->number, "body" => $request->body, 'mediaUrl' => $newMessageFiles)
                                    );

                            } else {
                                $message = $client->messages
                                    ->create($to,
                                        array("from" => $number->number, "body" => $request->body)
                                    );
                            }
                        } catch (\Exception $ex) {
                            $sendFailed[] = [
                                'message_id' => $newMessage->id,
                                'from_number' => $number->number,
                                'to_number' => $to,
                                'reason' => $ex->getMessage(),
                            ];
                        }
                    }
                } else if ($gateway == 'twilio') {
                    $credentials = json_decode(get_settings('twilio'));
                    if (!$credentials->tw_sid)
                        return back()->with('fail', 'Credentials not found. Please contact with the administrator');

                    $sid = $credentials->tw_sid;
                    $token = $credentials->tw_token;
                    $client = new \Twilio\Rest\Client($sid, $token);
                    foreach ($request->to_numbers as $to) {
                        try {
                            if ($messageFiles) {
                                $newMessageFiles = $messageFiles;

                                array_walk($newMessageFiles, function (&$value, $index) {
                                    $value = asset('uploads/' . $value);
                                });

                                $client->messages->create(
                                    $to,
                                    [
                                        'from' => $number->number,
                                        'body' => $request->body,
                                        'mediaUrl' => $newMessageFiles
                                    ]
                                );
                            } else {
                                $client->messages->create(
                                    $to,
                                    [
                                        'from' => $number->number,
                                        'body' => $request->body
                                    ]
                                );
                            }
                        } catch (\Exception $ex) {
                            Log::error($ex);
                            $sendFailed[] = [
                                'message_id' => $newMessage->id,
                                'from_number' => $number->number,
                                'to_number' => $to,
                                'reason' => $ex->getMessage(),
                            ];
                        }

                    }
                } else if ($gateway == 'nexmo') {
                    $credentials = json_decode(get_settings('nexmo'));
                    if (!$credentials->nx_api_key)
                        return back()->with('fail', 'Credentials not found. Please contact with the administrator');


                    $api_key = $credentials->nx_api_key;
                    $api_secret = $credentials->nx_api_secret;
                    $client = new \Vonage\Client(new Basic($api_key, $api_secret));
                    foreach ($request->to_numbers as $to) {
                        try {
                            $message = $client->message()->send([
                                'to' => $to,
                                'from' => $number->number,
                                'text' => $request->body
                            ]);
                        } catch (\Exception $ex) {
                            $sendFailed[] = [
                                'message_id' => $newMessage->id,
                                'from_number' => $number->number,
                                'to_number' => $to,
                                'reason' => $ex->getMessage(),
                            ];
                        }
                    }

                } else if ($gateway == 'telnyx') {
                    $credentials = json_decode(get_settings('telnyx'));
                    if (!$credentials->tl_api_key)
                        return back()->with('fail', 'Credentials not found. Please contact with the administrator');

                    \Telnyx\Telnyx::setApiKey($credentials->tl_api_key);
                    foreach ($request->to_numbers as $to) {
                        try {
                            if ($messageFiles) {
                                $newMessageFiles = $messageFiles;
                                array_walk($newMessageFiles, function (&$value, $index) {
                                    $value = asset('uploads/' . $value);
                                });
                                \Telnyx\Message::Create(['from' => $number->number, 'to' => $to, 'text' => $request->body, 'media_urls' => $newMessageFiles]);
                            } else {
                                \Telnyx\Message::Create(['from' => $number->number, 'to' => $to, 'text' => $request->body]);
                            }
                        } catch (\Exception $ex) {
                            $sendFailed[] = [
                                'message_id' => $newMessage->id,
                                'from_number' => $number->number,
                                'to_number' => $to,
                                'reason' => $ex->getMessage(),
                            ];
                        }
                    }

                } else if ($gateway == 'plivo') {
                    $credentials = json_decode(get_settings('plivo'));
                    if (!$credentials->pl_auth_id || !$credentials->pl_auth_token)
                        return back()->with('fail', 'Credentials not found. Please contact with the administrator');
                    $client = new RestClient($credentials->pl_auth_id, $credentials->pl_auth_token);
                    if ($messageFiles) {
                        $newMessageFiles = $messageFiles;
                        array_walk($newMessageFiles, function (&$value, $index) {
                            $value = asset('uploads/' . $value);
                        });

                        $message_created = $client->messages->create(
                            $number->number,
                            $request->to_numbers,
                            $request->body,
                            ['media_urls' => $newMessageFiles]
                        );
                    } else {
                        $message_created = $client->messages->create(
                            $number->number,
                            $request->to_numbers,
                            $request->body
                        );
                    }


                } else if ($gateway == 'textlocal') {
                    $credentials = json_decode(get_settings('textlocal'));
                    if (!$credentials->text_local_api_key || !$credentials->text_local_sender)
                        return back()->with('fail', 'Credentials not found. Please contact with the administrator');

                    $textlocal = new Textlocal(false, false, $credentials->text_local_api_key);

                    $numbers = $request->to_numbers;
                    $sender = $credentials->text_local_sender;
                    $message = $request->body;

                    $response = $textlocal->sendSms($numbers, $message, $sender);

                }
            }
            $current_plan->available_sms = $new_available_sms + count($sendFailed);
            $current_plan->save();

            if ($sendFailed) {
                SentFail::insert($sendFailed);
            }

            DB::commit();
            if ($sendFailed)
                return back()->with('success', 'Message sent partially');
            else
                return back()->with('success', 'Message sent successfully');
        } catch (\Exception $ex) {
            Log::error($ex);
            DB::rollBack();
            return back()->with('fail', $ex->getMessage());
        }
    }

}
