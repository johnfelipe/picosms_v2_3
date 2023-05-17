<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BillingRequest;
use App\Models\CustomerNumber;
use App\Models\Number;
use App\Models\NumberRequest;
use App\Models\Plan;
use Illuminate\Http\Request;

class NumberController extends Controller
{
    public function phone_numbers()
    {
        $data['numbers'] = auth('customer')->user()->numbers;
        return view('customer.numbers.index', $data);
    }

    public function get_numbers()
    {
        if (!request()->ajax()) return response()->json(['status' => 'Not found'], 404);
        $numbers = auth('customer')->user()->numbers()->select(['id', 'number', 'cost', 'created_at','forward_to','forward_to_dial_code']);
        return datatables()->of($numbers)
            ->addColumn('forward_to', function ($q) {
                if ($q->forward_to)
                    return "(" . $q->forward_to_dial_code . ")" . $q->forward_to;
                else
                    return "";
            })
            ->addColumn('purchased_at', function (CustomerNumber $q) {
                return $q->created_at->format('d-m-Y');
            })
            ->addColumn('action', function (CustomerNumber $q) {
                return ' <button data-id="'.$q->id.'" data-forward-to="'.$q->forward_to.'" data-forward-to-code="'.$q->forward_to_dial_code.'" type="button" class="btn-sm btn btn-info change-forward-to">Change Forward To</button>'.
                    ' <button class="btn btn-sm btn-danger" data-message="Are you sure you want to remove <b>\'' . $q->number . '\'</b> ?"
                                        data-action=' . route('customer.numbers.purchase.remove') . '
                                        data-input={"id":"' . $q->id . '"}
                                        data-toggle="modal" data-target="#modal-confirm"  >Remove</button> '
                    ;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function purchaseList()
    {
        return view('customer.numbers.purchase_list');
    }

    public function purchaseListGet()
    {
        if (!request()->ajax()) return response()->json(['status' => 'Not found'], 404);
        $numbers = auth('customer')->user()->admin->available_numbers()->select(['id', 'number', 'sell_price', 'created_at']);
        return datatables()->of($numbers)
            ->addColumn('cost', function (Number $q) {
                return '$' . $q->sell_price . '/month';
            })
            ->addColumn('action', function (Number $q) {

                return '<button class="btn btn-sm btn-info" data-message="Are you sure you want to buy <b>\'' . $q->number . '\'</b> ?"
                                        data-action=' . route('customer.numbers.purchase') . '
                                        data-input={"id":"' . $q->id . '"}
                                        data-toggle="modal" data-target="#modal-confirm"  >Buy</button>';
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function purchaseStore(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $number = Number::find($request->id);
        if (!$number) {
            return redirect()->back()->with('fail', 'Number not found');
        }
        $pre_number = auth('customer')->user()->numbers()->where('id', $number->id)->first();
        if (isset($pre_number) && $pre_number->id == $request->id) {
            return redirect()->back()->with('fail', 'You have already this number');
        }
        $customer = auth('customer')->user();
        $preReq = NumberRequest::where(['customer_id' => $customer->id, 'number_id' => $number->id, 'status' => 'pending'])->first();
        if ($preReq) {
            return redirect()->back()->with('fail', 'You already have a pending request. Please wait for the admin reply.');
        }
        $numberReq = new NumberRequest();
        $numberReq->admin_id = $number->admin_id;
        $numberReq->customer_id = $customer->id;
        $numberReq->number_id = $number->id;
        $numberReq->save();

        // TODO:: send email to customer here

        return redirect()->back()->with('success', 'We have received your request. We will contact with you shortly');
    }

    public function purchase_remove(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $number = auth('customer')->user()->numbers()->where('id', $request->id)->first();

        if (!$number) {
            return redirect()->back()->with('fail', 'Number not found');
        }
        $admin_number = $number->admin_number;
        $admin_number->status = 'active';
        $admin_number->save();

        $number->delete();

        //TODO:: Send a mail here to the customer and the admin as well
        return back()->with('success', 'Number has been removed from your account');

    }

    public function updateForwardTo(Request $request){

        $numbers = auth('customer')->user()->numbers()->where('id',$request->id)->first();
        if(!$numbers) return redirect()->back()->withErrors(['msg'=>'Number not found']);

        $numbers->forward_to_dial_code=$request->forward_to_dial_code;
        $numbers->forward_to=$request->forward_to;
        $numbers->save();
        return redirect()->back()->with('success','Forward number updated successfully');
    }
}
