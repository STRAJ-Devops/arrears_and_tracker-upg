<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::all();
        return view('branches', compact('branches'));
    }

    public function edit($id){
        $branch = Branch::find($id);
        return view('edit-branch', compact('branch'));
    }

    public function update(Request $request, $id){
        $requestData = $request->all();
        $branch = Branch::find($id);
        $branch->update($requestData);
        return redirect()->route('branches');

    }

    public function delete(){

    }
}
