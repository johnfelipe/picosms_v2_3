<?php

namespace App\Http\Controllers;

use App\Models\CustomerNumber;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Plivo\RestClient;
use Plivo\XML\Response;
use SignalWire\LaML\MessageResponse as LaML;
use SignalWire\Rest\Client;
use Textlocal;
use Twilio\Exceptions\ConfigurationException;
use Twilio\TwiML\MessagingResponse;
use Vonage\Client\Credentials\Basic;
use Vonage\Message\InboundMessage;

class InboundController extends Controller
{
    public function process($type, Request $request)
    {
        if ($type == 'signalwire') {
            if (!$request->has('MessageSid')) {
                return "error";
            }
            $MessageSid = $request->MessageSid;
            $SmsSid = $request->SmsSid;
            $AccountSid = $request->AccountSid;
            $From = $request->From;
            $To = $request->To;
            $Body = $request->Body;


            $customer_number = CustomerNumber::where('number', $To)
                ->orWhere('number', str_replace('+', '', $To))
                ->first();
            if (!$customer_number) {
                $resp = new LaML();
                echo $resp;
                exit();
            }

            if ($customer_number->forward_to) {
                $this->sendForwardMessage($type, $customer_number->number, $customer_number->forward_to_dial_code . $customer_number->forward_to, $Body);
            }

            $resp = new LaML();
            $customer = $customer_number->customer;

            $message = new Message();
            $message->customer_id = $customer->id;
            $message->body = $Body;
            $message->numbers = json_encode(['from' => $From, 'to' => [$To]]);
            $message->type = 'inbox';
            $message->message_obj = json_encode($request->except(['From', 'To', 'Body']));
            $message->save();
            echo $resp;

        } else if ($type == 'twilio') {
            $MessageSid = $request->MessageSid;
            $SmsSid = $request->SmsSid;
            $AccountSid = $request->AccountSid;
            $From = $request->From;
            $To = $request->To;
            $Body = $request->Body;

            $customer_number = CustomerNumber::where('number', $To)
                ->orWhere('number', str_replace('+', '', $To))
                ->first();
            if (!$customer_number) {
                Log::error("invalid number");
                $response = new MessagingResponse();
                echo $response;
                exit();
            }
            if ($customer_number->forward_to) {
                $this->sendForwardMessage($type, $customer_number->number, $customer_number->forward_to_dial_code . $customer_number->forward_to, $Body);
            }
            $response = new MessagingResponse();
            $customer = $customer_number->customer;

            $message = new Message();
            $message->customer_id = $customer->id;
            $message->body = $Body;
            $message->numbers = json_encode(['from' => $From, 'to' => [$To]]);
            $message->type = 'inbox';
            $message->message_obj = json_encode($request->only(['MessageSid', 'SmsSid', 'AccountSid']));
            $message->save();
            echo $response;

        } else if ($type == 'nexmo') {
            $inbound = \Vonage\SMS\Webhook\Factory::createFromGlobals();

                $MessageSid = $inbound->getMessageId();
                $From = $inbound->getFrom();
                $To = $inbound->getTo();
                $Body = $inbound->getText();

                $customer_number = CustomerNumber::where('number', $To)
                    ->orWhere('number', str_replace('+', '', $To))
                    ->first();
                if (!$customer_number) {
                   Log::error('Number not found for ' . $To);
                    exit();
                }

                if ($customer_number->forward_to) {
                    $this->sendForwardMessage($type, $customer_number->number, $customer_number->forward_to_dial_code . $customer_number->forward_to, $Body);
                }

                $customer = $customer_number->customer;
                $message = new Message();
                $message->customer_id = $customer->id;
                $message->body = $Body;
                $message->numbers = json_encode(['from' => $From, 'to' => [$To]]);
                $message->type = 'inbox';
                $message->message_obj = json_encode(['message_id' => $MessageSid]);
                $message->save();


        } else if ($type == 'telnyx') {
            $json = json_decode(file_get_contents("php://input"), true);
            Log::error($json);

            $From = $json["data"]["payload"]["from"]["phone_number"];
            $To = $json["data"]["payload"]["to"][0]["phone_number"];
            $Body = $json["data"]["payload"]["text"];
            $customer_number = CustomerNumber::where('number', $To)
                ->orWhere('number', str_replace('+', '', $To))
                ->first();
            if (!$customer_number) {
                $resp = new LaML();
                echo $resp;
                exit();
            }
            if($customer_number->forward_to){
                $this->sendForwardMessage($type,$customer_number->number,$customer_number->forward_to_dial_code.$customer_number->forward_to,$Body);
            }
            $resp = new LaML();
            $customer = $customer_number->customer;

            $message = new Message();
            $message->customer_id = $customer->id;
            $message->body = $Body;
            $message->numbers = json_encode(['from' => $From, 'to' => [$To]]);
            $message->type = 'inbox';
            $message->message_obj = json_encode([]);
            $message->save();
            echo $resp;

        } else if ($type == 'plivo') {

            $From = $_REQUEST["From"];
            $To = $_REQUEST["To"];
            $Body = $_REQUEST["Text"];
            $customer_number = CustomerNumber::where('number', $To)
                ->orWhere('number', str_replace('+', '', $To))
                ->first();
            if (!$customer_number) {
                $resp = new Response();
                echo $resp->toXML();
                exit();
            }
            if($customer_number->forward_to){
                $this->sendForwardMessage($type,$customer_number->number,$customer_number->forward_to_dial_code.$customer_number->forward_to,$Body);
            }
            $resp = new Response();
            $customer = $customer_number->customer;

            $message = new Message();
            $message->customer_id = $customer->id;
            $message->body = $Body;
            $message->numbers = json_encode(['from' => $From, 'to' => [$To]]);
            $message->type = 'inbox';
            $message->message_obj = json_encode([]);
            $message->save();
            echo $resp->toXML();
        }
    }

    function sendForwardMessage($type, $from, $to, $message)
    {
        try {

            if ($type == 'signalwire') {
                $credentials = json_decode(get_settings('signalwire'));
                if (!$credentials->sw_project_id || !$credentials->sw_token || !$credentials->sw_space_url)
                    exit();

                try {
                    $client = new Client($credentials->sw_project_id, $credentials->sw_token, array("signalwireSpaceUrl" => $credentials->sw_space_url));
                    $message = $client->messages
                        ->create($to,
                            array("from" => $from, "body" => $message)
                        );
                } catch (\Exception $e) {

                }

            } elseif ($type == 'twilio') {
                $credentials = json_decode(get_settings('twilio'));
                if (!$credentials->tw_sid || !$credentials->tw_token)
                    exit();

                $sid = $credentials->tw_sid;
                $token = $credentials->tw_token;
                try {
                    $client = new \Twilio\Rest\Client($sid, $token);
                    $client->messages->create(
                        $to,
                        [
                            'from' => $from,
                            'body' => $message
                        ]
                    );
                } catch (\Exception $e) {
                }


            } elseif ($type == 'nexmo') {
                $credentials = json_decode(get_settings('nexmo'));
                if (!$credentials->nx_api_key || !$credentials->nx_api_secret)
                    exit();

                $api_key = $credentials->nx_api_key;
                $api_secret = $credentials->nx_api_secret;
                $client = new \Vonage\Client(new Basic($api_key, $api_secret));
                $message = $client->message()->send([
                    'to' => $to,
                    'from' => $from,
                    'text' => $message
                ]);
            } elseif ($type == 'telnyx') {
                $credentials = json_decode(get_settings('telnyx'));
                if (!$credentials->tl_api_key)
                    exit();

                \Telnyx\Telnyx::setApiKey($credentials->tl_api_key);
                try {
                    \Telnyx\Message::Create(['from' => $from, 'to' => $to, 'text' => $message]);
                } catch (\Exception $ex) {

                }
            } elseif ($type == 'plivo') {
                $credentials = json_decode(get_settings('plivo'));
                if (!$credentials->pl_auth_id || !$credentials->pl_auth_token)
                    exit();

                $client = new RestClient($credentials->pl_auth_id, $credentials->pl_auth_token);
                $message_created = $client->messages->create(
                    $from,
                    [$to],
                    $message
                );
            } elseif ($type == 'textlocal') {
                $credentials = json_decode(get_settings('textlocal'));
                if (!$credentials->text_local_api_key || !$credentials->text_local_sender)
                    exit();

                $textlocal = new Textlocal(false, false, $credentials->text_local_api_key);

                $numbers = $to;
                $sender = $credentials->text_local_sender;
                $message = $message;

                $response = $textlocal->sendSms($numbers, $message, $sender);

            }
        }catch (\Exception $ex){
            Log::error("forward message error");
        }
    }
}
