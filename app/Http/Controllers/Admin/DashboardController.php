<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){

        $user =auth()->user();
        $customers=$user->customers;
        $customer_ids=[];
        foreach ($customers as $key=>$customer){
            $customer_ids[]=$customer->id;
        }
        $inboxes=Message::whereIn('customer_id',$customer_ids)->where('type','inbox')->get();
        $sent=Message::whereIn('customer_id',$customer_ids)->where('type','sent')->get();


        $data['newMessageCount'] = $inboxes->where('created_at', '>=', Carbon::now())->count();
        $data['newSentCount'] = $sent->where('created_at', '>=', Carbon::now())->count();

        $data['totalInbox'] = $inboxes->count();
        $data['totalSent'] = $sent->count();



        $inboxes = Message::whereIn('customer_id',$customer_ids)->where('type','inbox')
            ->select(DB::Raw('count(*) as count'),DB::Raw('DATE(created_at) day'))
            ->where('created_at', '>', Carbon::now()->subDays(30))
            ->groupBy('day')->get()
            ->pluck('count','day');

        $data['weekDates']=getLastNDays(30);
        $chatInboxes=[];
        foreach (getLastNDays(30) as $day){
            $chatInboxes[]=isset($inboxes[trim($day, '"')])?$inboxes[trim($day, '"')]:0;
        }
        $data['chart_inbox']=$chatInboxes;

        $sents = Message::whereIn('customer_id',$customer_ids)->where('type','sent')
            ->select(DB::Raw('count(*) as count'),DB::Raw('DATE(created_at) day'))
            ->where('created_at', '>', Carbon::now()->subDays(30))
            ->groupBy('day')->get()
            ->pluck('count','day');
        $chat_sents=[];
        foreach (getLastNDays(30) as $day){
            $chat_sents[]=isset($sents[trim($day, '"')])?$sents[trim($day, '"')]:0;
        }
        $data['chart_sent']=$chat_sents;
        return view('admin.dashboard',$data);
    }
}
