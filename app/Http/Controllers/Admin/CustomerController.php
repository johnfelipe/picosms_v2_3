<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingRequest;
use App\Models\Customer;
use App\Models\Number;
use App\Models\Plan;
use foo\bar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index()
    {
        return view('admin.customers.index');
    }

    public function getAll()
    {
        $customers = Customer::select(['id', 'first_name', 'last_name', 'email', 'status', 'created_at']);

        return datatables()->of($customers)
            ->addColumn('full_name', function ($q) {
                return $q->full_name;
            })
            ->addColumn('action', function (Customer $q) {
                return "<a class='btn btn-sm btn-info' href='" . route('admin.customers.edit', [$q->id]) . "'>Edit</a>  &nbsp; &nbsp;".
                    '<button class="btn btn-sm btn-primary" data-message="You will be logged in as customer?"
                                        data-action='.route('admin.customer.login.ass').'
                                        data-input={"id":'.$q->id.'}
                                        data-toggle="modal" data-target="#modal-confirm">Login As</button>' ;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:customers',
            'password' => 'required',
            'status' => 'required'
        ]);

        $request['email_verified_at']=now();

        $customer=auth()->user()->customers()->create($request->all());
        //Assigning plan to customer
        $plan = Plan::first();
        $customer->plan()->create(['plan_id' => $plan->id, 'sms_limit' => $plan->sms_limit, 'available_sms' => $plan->sms_limit, 'price' => $plan->price]);

        return back()->with('success', 'Customer successfully created');
    }

    public function edit(Customer $customer)
    {
        $data['customer'] = $customer;
        $data['availableNumbers'] = auth()->user()->available_numbers;
        $data['activePlans'] = auth()->user()->active_plans;
        return view('admin.customers.edit', $data);
    }

    public function update(Customer $customer, Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|unique:customers,email,' . $customer->id,
            'status' => 'required'
        ]);

        //Check for password availability
        if (!$request->password) unset($request['password']);

        //update the model
        $customer->update($request->all());

        return back()->with('success', 'Customer successfully updated');
    }

    public function assignNumber(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'customer_id' => 'required|exists:customers,id',
        ]);

        $customer = auth()->user()->customers()->where('id', $request->customer_id)->first();
        if (!$customer) return back()->with('fail', 'Customer not found');

        $number = Number::find($request->id);
        if (!$number) return back()->with('fail', 'Number not found');

        $isAssigned = $customer->numbers()->where('number_id', $number->id)->first();
        if ($isAssigned) return back()->with('fail', 'Number already assigned to this customer');

        $customer->numbers()->create(['number_id' => $number->id, 'number' => $number->number, 'cost' => $number->sell_price]);
        return back()->with('success', 'Number successfully added to the customer');
    }

    public function removeNumber(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'customer_id' => 'required|exists:customers,id',
        ]);

        $customer = auth()->user()->customers()->where('id', $request->customer_id)->first();
        if (!$customer) return back()->with('fail', 'Customer not found');

        $number = Number::find($request->id);
        if (!$number) return back()->with('fail', 'Number not found');

        $isAssigned = $customer->numbers()->where('number_id', $number->id)->first();
        if (!$isAssigned) return back()->with('fail', 'Number haven\'t assigned to this customer');

        $isAssigned->delete();

        return back()->with('success', 'Number successfully removed from the customer');
    }

    public function changePlan(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'customer_id' => 'required',
        ]);

        $customer = auth()->user()->customers()->where('id', $request->customer_id)->first();
        if (!$customer) return back()->with('fail', 'Customer not found');

        $plan = Plan::find($request->id);
        if (!$plan) return back()->with('fail', 'Plan not found');

        $pre_plan = $customer->plan;

        $isAssigned = $pre_plan->plan_id == $plan->id;
        if ($isAssigned) return back()->with('fail', 'This Plan is already assigned to this customer');

        if (isset($request->from)) {

            if ($request->from == 'request' && $request->billing_id && in_array($request->status, ['accepted', 'rejected'])) {
                $billingRequest = BillingRequest::find($request->billing_id);
                if (!$billingRequest)
                    return back()->with('fail', 'Billing request not found');

                $billingRequest->status = $request->status;
                $billingRequest->save();

                if ($request->status == 'rejected') return back()->with('success', 'Status successfully cancelled for the customer');

            } else
                return back()->with('fail', 'Invalid data for billing request');
        }

        //delete previous plan
        //TODO: suggestion: might need to change plan status in future without deleting plan
        if ($pre_plan) {
            $customer->plan()->delete();
        }
        $customer->plan()->create(['plan_id' => $plan->id, 'sms_limit' => $plan->sms_limit,'available_sms'=>$plan->sms_limit, 'price' => $plan->price]);


        // TODO:: send email here


        return back()->with('success', 'Plan successfully updated for the customer');
    }

    public function loginAs(Request $request){
        if(!$request->id) abort(404);
        auth('customer')->loginUsingId($request->id);
        return redirect()->route('customer.dashboard')->with('success',trans('You are now logged as customer'));
    }

}
