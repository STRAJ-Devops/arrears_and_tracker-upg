<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArrearsAndSalesController extends Controller
{
    public function index()
    {
        return view('arrears-and-sales-uploader');
    }
}
