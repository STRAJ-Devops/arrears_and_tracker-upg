<?php

namespace App\Http\Controllers;

use App\Imports\BranchTargetsImport;
use App\Models\BranchTarget;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        //save the file to the server
        $file = $request->file('branch_targets_file');
        $file_name = time() . '_' . $file->getClientOriginalName();
        $save = $file->move(public_path('uploads'), $file_name);

        try {
            BranchTarget::truncate();

            $file = public_path('uploads/' . $file_name);
            $csv = array_map('str_getcsv', file($file));

            for ($i = 1; $i < count($csv); $i++) {
                //check if the branch target already exists
                $existingRecord = BranchTarget::where('branch_id', $csv[$i][0])->first();
                if (!$existingRecord) {
                    $branch_target = new BranchTarget();
                    $branch_target->branch_id = $csv[$i][0];
                    $branch_target->target_amount = $csv[$i][2];
                    $branch_target->target_numbers = $csv[$i][3];
                    $branch_target->save();
                } else {
                    //update the branch target if it already exists
                    $existingRecord->target_amount = $csv[$i][2];
                    $existingRecord->target_numbers = $csv[$i][3];
                    $existingRecord->save();
                }

            }
        } catch (\Exception $e) {
            // Return an error message if import fails
            return response()->json(['error' => 'Failed to import branch targets. Please ensure the file format is correct.', 'exception' => $e], 400);
        }

        // Return a success message upon successful import
        return response()->json(['message' => 'Branch targets imported successfully.'], 200);
    }

    /**
     * Download the branch targets template
     */
    public function downloadTemplate()
    {
        $file = public_path('assets\templates\branch_targets_template.csv');
        return response()->download($file);
    }
}
