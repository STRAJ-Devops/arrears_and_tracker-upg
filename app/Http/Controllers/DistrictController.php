<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DistrictController extends Controller
{
    public function index()
    {
        return view('districts');
    }

    public function import(Request $request)
    {
        $file = $request->file('file');

        Excel::import(new UsersImport, $file);

        return redirect()->back()->with('success', 'Data imported successfully!');
    }
}
