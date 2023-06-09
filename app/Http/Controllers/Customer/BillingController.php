<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BillingRequest;
use App\Models\CustomerPlan;
use App\Models\Number;
use App\Models\Plan;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function index(){
        $data['plans']=Plan::where('status','active')->get();
        $data['customer_plan']=auth('customer')->user()->plan;
        return view('customer.billings.index',$data);
    }

    public function update(Request $request){
        $request->validate([
            'id'=>'required|exists:plans'
        ]);
        $plan=Plan::find($request->id);
        if(!$plan){
            return redirect()->back()->with('fail','You plan not found');
        }
        $pre_plan=auth('customer')->user()->plan;
        if(isset($pre_plan) && $pre_plan->plan_id==$request->id){
            return redirect()->back()->with('fail','You are already subscribed to this plan');
        }
        $customer=auth('customer')->user();
        $preBilling=BillingRequest::where(['customer_id'=>$customer->id,'status'=>'pending'])->first();
        if($preBilling){
            return redirect()->back()->with('fail','You already have a pending request. Please wait for the admin reply.');
        }
        $planReq=new BillingRequest();
        $planReq->admin_id=$plan->admin_id;
        $planReq->customer_id=$customer->id;
        $planReq->plan_id=$plan->id;
        $planReq->save();

        // TODO:: send email to customer here

        return redirect()->back()->with('success','We have received your request. We Will contact with you shortly');
    }

}
