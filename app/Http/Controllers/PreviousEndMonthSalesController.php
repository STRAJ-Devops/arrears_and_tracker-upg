<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PreviousEndMonthSalesController extends Controller
{
    public function index()
    {
        return view('previous-end-month-sales-uploader');
    }
}
