<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use App\Models\WrittenOff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WrittenOffController extends Controller
{
    public function index(){
        return view('written-off-customers');
    }
    public function writtenOffUploader()
    {
        return view('written-off-customers-uploader');
    }
    public function importWrittenOffs(Request $request)
    {
        ini_set('max_execution_time', 1200);
        ini_set('memory_limit', '-1');
        // Validate the uploaded file
        $request->validate([
            'upload_written_off_file' => 'required|mimes:csv, xls, xlsx',
        ], [
            'upload_written_off_file.required' => 'Please upload a file.',
        ]);

        //save the file to the server
        $file = $request->file('upload_written_off_file');
        $file_name = time() . '_' . $file->getClientOriginalName();
        $save = $file->move(public_path('uploads'), $file_name);

        // Check if the file was successfully saved
        if (!$save) {
            return response()->json(['error' => 'Failed to save file. Please try again.'], 400);
        } else {
            // Check if the file is a CSV
            if ($file->getClientOriginalExtension() == 'csv') {
                //read the csv file
                $file = public_path('uploads/' . $file_name);
                $csv = array_map('str_getcsv', file($file));

                //truncate the sales and written offs table
                WrittenOff::truncate();

                for ($i = 9; $i < count($csv); $i++) {
                    try {
                        /**
                         * current loan officer
                         */
                        if (filled($csv[$i][2]) && !(Str::startsWith($csv[$i][2], 'Total'))) {
                            $officer_name = $csv[$i][2];
                        }
                        $written_off = new WrittenOff();
                        $written_off->officer_name = $officer_name;
                        $written_off->contract_id = $csv[$i][1];
                        $written_off->customer_id = $csv[$i][2];
                        $written_off->csa = $csv[$i][3];
                        $written_off->dda = $csv[$i][4];
                        $written_off->write_off_date = $csv[$i][5];
                        $written_off->principal_written_off = $csv[$i][6];
                        $written_off->interest_written_off = $csv[$i][7];
                        $written_off->principal_paid = $csv[$i][8];
                        $written_off->interest_paid = $csv[$i][9];

                    } catch (\Exception $e) {
                        return response()->json(['error' => 'Failed to process the file. Please ensure the file format(CSV/Excel) is correct.', 'exception' => $e->getMessage()], 400);
                    }
                }
            }
        }

        // Return a success message upon successful import
        return response()->json(['message' => 'Sales and arrears imported successfully.'], 200);
    }
}
