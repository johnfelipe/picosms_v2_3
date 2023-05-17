<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingRequest;
use App\Models\Customer;
use App\Models\CustomerPlan;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return view('admin.plans.index');
    }

    public function getAll()
    {

        $customers = auth()->user()->plans()->select(['id', 'title', 'sms_limit', 'price', 'status', 'created_at']);
        return datatables()->of($customers)
            ->addColumn('created_at', function ($q) {
                return $q->created_at->format('d-m-Y');
            })
            ->addColumn('action', function (Plan $q) {
                return "<a class='btn btn-sm btn-info' href='" . route('admin.plans.edit', [$q->id]) . "'>Edit</a>";
            })
            ->rawColumns(['action'])
            ->toJson();
    }


    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:plans',
            'price' => 'required|numeric',
            'limit' => 'required|numeric',
            'status' => 'required|in:active,inactive'
        ]);

        $request['sms_limit'] = $request->limit;
        unset($request['limit']);

        auth()->user()->plans()->create($request->all());

        return back()->with('success', 'Plan successfully created');
    }

    public function edit(Plan $plan)
    {
        $data['plan'] = $plan;
        return view('admin.plans.edit', $data);
    }

    public function update(Plan $plan, Request $request)
    {
        $request->validate([
            'title' => 'required|unique:plans,title,' . $plan->id,
            'price' => 'required|numeric',
            'limit' => 'required|numeric',
            'status' => 'required|in:active,inactive'
        ]);

        $request['sms_limit'] = $request->limit;
        unset($request['limit']);

        $valid_data = $request->only('title', 'sms_limit', 'price', 'status');

        //update the model
        $plan->update($valid_data);

        return back()->with('success', 'Plan successfully updated');
    }

    public function requests()
    {
        return view('admin.plans.requests');
    }

    public function get_requests()
    {

        $requests = auth()->user()->plan_requests;
        return datatables()->of($requests)
            ->addColumn('title', function (BillingRequest $q) {
                return $q->plan->title;
            })
            ->addColumn('price', function (BillingRequest $q) {
                return $q->plan->price;
            })
            ->addColumn('transaction_id', function (BillingRequest $q) {
                return $q->transaction_id;
            })
            ->addColumn('other_info', function (BillingRequest $q) {
                if ($q->other_info) {
                    $array = (array)json_decode($q->other_info);
                    $obj = json_encode(array_combine(array_map("ucfirst", array_keys($array)), array_values($array)));
                } else
                    $obj = "";
                return "<div class='show-more' style='max-width: 500px;white-space: pre-wrap'>" . str_replace(['_', '"', "{", "}"], [' ', ' ', '', ''], $obj) . "</div>";
            })
            ->addColumn('status', function (BillingRequest $q) {
                return $q->status;
            })
            ->addColumn('action', function (BillingRequest $q) {
                return '<button class="mr-1 btn btn-sm btn-info" data-message="Are you sure you want to assign <b>\'' . $q->plan->title . '\'</b> to \'' . $q->customer->full_name . '\' ?"
                                        data-action=' . route('admin.customer.plan.change') . '
                                        data-input={"id":"' . $q->plan_id . '","customer_id":"' . $q->customer_id . '","from":"request","billing_id":"' . $q->id . '","status":"accepted"}
                                        data-toggle="modal" data-target="#modal-confirm"  >Approve</button>' .
                    '<button class="btn btn-sm btn-danger" data-message="Are you sure you want to reject <b>\'' . $q->plan->title . '\'</b> for \'' . $q->customer->full_name . '\' ?"
                                        data-action=' . route('admin.customer.plan.change') . '
                                        data-input={"id":"' . $q->plan_id . '","customer_id":"' . $q->customer_id . '","from":"request","billing_id":"' . $q->id . '","status":"rejected"}
                                        data-toggle="modal" data-target="#modal-confirm"  >Reject</button>';
            })
            ->addColumn('customer', function (BillingRequest $q) {
                return "<a href='" . route('admin.customers.edit', [$q->customer_id]) . "'>" . $q->customer->full_name . "</a>";
            })
            ->rawColumns(['action', 'customer', 'other_info'])
            ->toJson();
    }

}
