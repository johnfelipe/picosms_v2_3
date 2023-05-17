<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{


    public function index()
    {
        $user = auth()->guard('customer')->user();

        $data['newMessageCount'] = $user->receive_messages()->where('created_at', '>=', Carbon::now())->count();
        $data['inboxCount'] = $user->receive_messages()->count();
        $data['sentCount'] = $user->sent_messages()->count();
        $inbox = $user->receive_messages()->where('created_at', '>', Carbon::now()->startOfDay())->get();
        $outbox = $user->sent_messages()->where('created_at', '>', Carbon::now()->startOfDay())->get();


        $inboxes = $user->receive_messages()
            ->select(DB::Raw('count(*) as count'),DB::Raw('DATE(created_at) day'))
            ->where('created_at', '>', Carbon::now()->startOfWeek())
            ->groupBy('day')->get()
            ->pluck('count','day');
        $data['weekDates']=getLastNDays(7);
        $chatInboxes=[];
        foreach (getLastNDays(7) as $day){
            $chatInboxes[]=isset($inboxes[trim($day, '"')])?$inboxes[trim($day, '"')]:0;
        }
        $data['chart_inbox']=$chatInboxes;
        $plan=auth('customer')->user()->plan;
        $data['remaining_sms']=$plan->available_sms??0;

        return view('customer.dashboard', $data);
    }
}
