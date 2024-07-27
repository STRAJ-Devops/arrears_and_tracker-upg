<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use App\Models\OfficerTarget;
use Illuminate\Http\Request;

class OfficerTargetController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        if (!empty($keyword)) {
            $targets = OfficerTarget::with('officer')
                ->whereHas('officer', function ($query) use ($keyword) {
                    $query->where('names', 'LIKE', "%$keyword%");
                })
                ->orWhere('officer_id', 'LIKE', "%$keyword%")
                ->paginate($perPage);
        } else {
            $targets = OfficerTarget::with('officer')->paginate($perPage);
        }

        return view('officer-targets-uploader', compact('targets'));
    }

    public function uploadOfficerTargets()
    {
        return view('upload-officer-targets');
    }

    public function deleteOfficerTargets()
    {
        //empty the OfficerTarget table
        $delete = OfficerTarget::truncate();
        if (!$delete) {
            return response()->json(['error' => 'Failed to delete officer targets. Please try again.'], 400);
        }
        return redirect()->back()->with('success', 'Officer targets deleted successfully.');
    }

    public function import(Request $request)
    {
        // validate
        $this->validate($request, [
            'officer_targets_file' => 'required|mimes:csv'
        ]);
        //save the file to the server
        $file = $request->file('officer_targets_file');
        $file_name = time() . '_' . $file->getClientOriginalName();
        $save = $file->move(public_path('uploads'), $file_name);

        try {
            OfficerTarget::truncate();

            $file = public_path('uploads/' . $file_name);
            $csv = array_map('str_getcsv', file($file));

            for ($i = 1; $i < count($csv); $i++) {
                //split the first column by - to get officer id and name
                $officer_id = explode('-', $csv[$i][0])[0]??null;
                //check if the officer target already exists
                $existingRecord = OfficerTarget::where('officer_id', $officer_id)->first();
                // remove commas from target amount
                $csv[$i][1] = str_replace(',', '', $csv[$i][1]);

                //check in officer table if the officer exists
                $officer = Officer::where('staff_id', $officer_id)->first();
                if (!$officer) {
                    continue;
                }
                if (!$existingRecord) {
                    $officer_target = new OfficerTarget();
                    $officer_target->officer_id = $officer_id;
                    $officer_target->target_amount = $csv[$i][1];
                    $officer_target->target_numbers = $csv[$i][2];
                    $officer_target->save();
                } else {
                    //update the officer target if it already exists
                    $existingRecord->target_amount = $csv[$i][1];
                    $existingRecord->target_numbers = $csv[$i][2];
                    $existingRecord->save();
                }

            }
        } catch (\Exception $e) {
            // Return an error message if import fails
            return response()->json(['error' => 'Failed to import officer targets. Please ensure the file format is correct.', 'exception' => $e], 400);
        }

        // Return a success message upon successful import
        return response()->json(['message' => 'Officer targets imported successfully.'], 200);
    }

    /**
     * Download the officer targets template
     */
    public function downloadTemplate()
    {
        $file = public_path('assets\templates\officer_targets_template.csv');
        return response()->download($file);
    }
}
