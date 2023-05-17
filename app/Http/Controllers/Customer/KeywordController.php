<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    public function index()
    {
        return view('customer.keywords.index');
    }

    public function create()
    {
        $data['keywords'] = auth('customer')->user()->keywords;
        $data['contacts'] = auth('customer')->user()->contacts;
        return view('customer.keywords.create', $data);
    }

}
