<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Imports\ContactsImport;
use App\Models\BillingRequest;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Number;
use App\Models\NumberRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GroupController extends Controller
{
    public function index()
    {
        return view('customer.groups.index');
    }


    public function getAll()
    {
        $customers = auth('customer')->user()->groups()->select(['id', 'name', 'status', 'created_at']);
        return datatables()->of($customers)
            ->addColumn('created_at', function ($q) {
                return $q->created_at->format('d-m-Y');
            })
            ->addColumn('contacts', function ($q) {
                $c = [];
                foreach ($q->contacts as $contact) {
                    $c[] = trim($contact->contact->number);
                }
                return "<div class='show-more' style='max-width: 500px;white-space: pre-wrap'>" . implode(", ", $c) . "</div>";
            })
            ->addColumn('action', function ($q) {
                return "<a class='btn btn-sm btn-info' href='" . route('customer.groups.edit', [$q->id]) . "'>Edit</a> &nbsp; &nbsp;" .
                    '<button class="btn btn-sm btn-danger" data-message="Are you sure you want to delete this group? <br><span class=\'text-danger text-sm\'>This will delete all the contacts assigned to this group</span></br>"
                                        data-action=' . route('customer.groups.destroy', [$q]) . '
                                        data-input={"_method":"delete"}
                                        data-toggle="modal" data-target="#modal-confirm">Delete</button>';
            })
            ->rawColumns(['action', 'contacts'])
            ->toJson();
    }

    public function create()
    {
        $data['contacts'] = auth('customer')->user()->contacts;
        return view('customer.groups.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required|in:active,inactive'
        ]);

        $preGroup = auth('customer')->user()->groups()->where('name', $request->name)->first();
        if ($preGroup) return back()->withErrors(['msg' => "Group name already exists"]);


        $group = new Group();
        $group->customer_id = auth('customer')->id();
        $group->name = $request->name;
        $group->status = $request->status;
        $group->save();
        $contactArray = [];
        if (isset($request->contact_ids)) {
            foreach ($request->contact_ids as $contact_id) {
                $contactArray[] = [
                    'contact_id' => $contact_id,
                    'customer_id' => $group->customer_id,
                    'group_id' => $group->id,
                ];
            }
        }

        $group->contacts()->insert($contactArray);

        if ($request->hasFile('contact_csv')) {
            $data = $request->file('contact_csv');
            $fileName=$group->id . '.' . $data->getClientOriginalExtension();
            $data->move(public_path('uploads'),$fileName);
            //You can choose to validate file type. e.g csv,xls,xlsx.
            $file_url = public_path('uploads/') .$fileName;
            try{
                Excel::import(new ContactsImport($group->id), $file_url);
            }catch (\Exception $ex){
                if(isset($ex->validator)){
                    return redirect()->back()->withErrors($ex->validator->errors());
                }else{
                    return redirect()->back()->withErrors(['msg'=>$ex->getMessage()]);
                }

            }
        }

        return back()->with('success', 'Group successfully created');
    }

    public function edit(Group $group)
    {
        $data['group'] = $group;
        $data['contacts'] = auth('customer')->user()->contacts;
        $groupContactIds = [];

        foreach ($group->contacts as $contact) {
            $groupContactIds[] = trim($contact->contact_id);
        }


        $data['groupContactIds'] = $groupContactIds;

        return view('customer.groups.edit', $data);
    }

    public function update(Group $group, Request $request)
    {
        $request->validate([
            'name' => 'required|unique:groups,name,' . $group->id,
            'status' => 'required|in:active,inactive'
        ]);

        $valid_data = $request->only('name', 'status');

        //update the model
        $group->update($valid_data);

        $group->contacts()->delete();


        if ($request->hasFile('contact_csv')) {
            $data = $request->file('contact_csv');
            $data->move(('uploads'), $group->id . '.' . $data->getClientOriginalExtension());
            //You can choose to validate file type. e.g csv,xls,xlsx.
            $file_url = ('uploads/') . $data->getClientOriginalName();
            Excel::import(new ContactsImport($group->id), $file_url);
        }

        if (isset($request->contact_ids)) {
            $contactArray = [];
            foreach ($request->contact_ids as $contact_id) {
                $contactArray[] = [
                    'contact_id' => $contact_id,
                    'customer_id' => $group->customer_id,
                    'group_id' => $group->id,
                ];
            }
            $group->contacts()->insert($contactArray);
        }


        return back()->with('success', 'Group successfully updated');
    }

    public function destroy(Group $group)
    {
        $contacts = $group->contacts()->pluck('contact_id');
        Contact::whereIn('id', $contacts)->delete();
        ContactGroup::whereIn('contact_id', $contacts)->delete();
        $group->delete();

        return back()->with('success', 'Group and assigned contacts successfully deleted');
    }

}
