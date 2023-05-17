<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Number;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        return view('customer.contacts.create');
    }

    public function index()
    {
        return view('customer.contacts.index');
    }

    public function getAll()
    {
        $contacts = auth('customer')->user()->contacts()->select(['id', 'number', 'first_name', 'last_name', 'email', 'company']);
        return datatables()->of($contacts)
            ->addColumn('action', function ($q) {
                return "<a class='btn btn-sm btn-info' href='" . route('customer.contacts.edit', [$q->id]) . "'>Edit</a> &nbsp; &nbsp;" .
                    '<button class="btn btn-sm btn-danger" data-message="Are you sure you want to delete this number?"
                                        data-action=' . route('customer.contacts.destroy', [$q]) . '
                                        data-input={"_method":"delete"}
                                        data-toggle="modal" data-target="#modal-confirm">Delete</button>';
            })
            ->addColumn('number', function ($q) {
                return $q->number;
            })
            ->addColumn('name', function ($q) {
                return $q->first_name . ' ' . $q->last_name;
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|unique:contacts|regex:/^[0-9\-\+]{9,15}$/',
            'contact_dial_code' => 'required'
        ]);

        auth('customer')->user()->contacts()->create($request->all());

        return back()->with('success', 'Contact successfully added');
    }

    public function edit(Contact $contact)
    {
        $data['contact'] = $contact;
        return view('customer.contacts.edit', $data);
    }

    public function update(Contact $contact, Request $request)
    {
        $request->validate([
            'first_name' => 'required',
        ]);

        $valid_data = $request->only('first_name', 'last_name', 'email', 'company','forward_to', 'forward_to_dial_code');

        //update the model
        $contact->update($valid_data);

        return back()->with('success', 'Contact successfully updated');
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();
        return back()->with('success', 'Contact successfully deleted');
    }
}
