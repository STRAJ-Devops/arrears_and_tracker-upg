<?php

namespace App\Http\Controllers;

use App\Imports\BranchTargetsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BranchTargetController extends Controller
{
    public function index()
    {
        return view('branch-targets-uploader');
    }

    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'branch_targets_file' => 'required|mimes:xlsx,xls,csv'
        ], [
            'branch_targets_file.required' => 'Please upload a file.',
            'branch_targets_file.mimes' => 'The uploaded file must be a valid Excel or CSV file.'
        ]);

        try {
            // Import the file using BranchTargetsImport class
            Excel::import(new BranchTargetsImport, $request->file('branch_targets_file'));
        } catch (\Exception $e) {
            // Return an error message if import fails
            return response()->json(['error' => 'Failed to import branch targets. Please ensure the file format is correct.', 'exception'=>$e], 400);
        }

        // Return a success message upon successful import
        return response()->json(['message' => 'Branch targets imported successfully.'], 200);
    }
}
