<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TargetsUploaderController extends Controller
{
    public function index()
    {
        return view('targets-uploader');
    }
}
