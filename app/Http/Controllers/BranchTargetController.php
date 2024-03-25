<?php

namespace App\Http\Controllers;

use App\Imports\BranchTargetsImport;
use App\Models\Branch;
use App\Models\BranchTarget;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
class BranchTargetController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        if (!empty($keyword)) {
            $targets = BranchTarget::with('branch')
                ->whereHas('branch', function ($query) use ($keyword) {
                    $query->where('branch_name', 'LIKE', "%$keyword%");
                })
                ->orWhere('branch_id', 'LIKE', "%$keyword%")
                ->paginate($perPage);
        } else {
            $targets = BranchTarget::with('branch')->paginate($perPage);
        }

        return view('branch-targets-uploader', compact('targets'));
    }

    public function uploadBranchTargets()
    {
        return view('upload-branch-targets');
    }

    public function deleteBranchTargets()
    {
        //empty the BranchTarget table
        $delete = BranchTarget::truncate();
        if (!$delete) {
            return response()->json(['error' => 'Failed to delete branch targets. Please try again.'], 400);
        }
        return redirect()->back()->with('success', 'Branch targets deleted successfully.');
    }


    public function import(Request $request)
    {
        //truncate the BranchTarget table
        $Ids = DB::table('branch_targets')->pluck('id');

        BranchTarget::destroy($Ids);

        //check if BranchTarget table is empty
        if (BranchTarget::count() > 0) {
            return redirect()->back()->with('error', 'Failed to delete branch targets. Please try again.');
        }
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
