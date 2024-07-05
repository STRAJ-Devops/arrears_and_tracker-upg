<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use App\Models\WrittenOff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WrittenOffController extends Controller
{
    public function index()
    {
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
                        } else {
                            $written_off = new WrittenOff();
                            $written_off->officer_name = $officer_name ?? '';
                            $written_off->contract_id = $csv[$i][3];
                            $written_off->customer_id = $csv[$i][4];
                            $written_off->customer_name = $csv[$i][5];
                            $written_off->customer_phone_number = $csv[$i][6];
                            $written_off->group_id = explode(' ', $csv[$i][7])[0];
                            $written_off->group_name = substr($csv[$i][7], strpos($csv[$i][7], ' ') + 1);
                            $written_off->csa = $csv[$i][10];
                            $written_off->dda = $csv[$i][11];
                            $written_off->write_off_date = $csv[$i][14];
                            $written_off->principal_written_off = $csv[$i][15];
                            $written_off->interest_written_off = $csv[$i][16];
                            $written_off->principal_paid = $csv[$i][17];
                            $written_off->interest_paid = $csv[$i][18];

                            $written_off->save();
                        }

                    } catch (\Exception $e) {
                        return response()->json(['error' => 'Failed to process the file. Please ensure the file format(CSV/Excel) is correct.', 'exception' => $e->getMessage()], 400);
                    }
                }
            }
        }

        // Return a success message upon successful import
        return response()->json(['message' => 'written offs uploaded'], 200);
    }

    public function customer(Request $request)
    {
        $customer_id = $request->customer_id;
        $search_by = $request->search_by;

        //check if search_by is customer_id, phone or name

        if ($search_by == 'customer_id') {
            $customer_details = DB::table('written_offs')
                ->selectRaw('
                    customer_id,
                    customer_name,
                    group_id,
                    group_name,
                    customer_phone_number,
                    csa,
                    dda,
                    write_off_date,
                    principal_written_off,
                    interest_written_off,
                    principal_paid,
                    interest_paid')
                ->where('customer_id', $customer_id)
                ->get();
        } elseif ($search_by == 'phone') {
            $customer_details = DB::table('written_offs')
                ->selectRaw('
                    customer_id,
                    customer_name,
                    group_id,
                    group_name,
                    customer_phone_number,
                    csa,
                    dda,
                    write_off_date,
                    principal_written_off,
                    interest_written_off,
                    principal_paid,
                    interest_paid')
                ->where('customer_phone_number', 'like', '%' . $customer_id . '%')
                ->get();
        } elseif ($search_by == 'name') {
            $customer_details = DB::table('written_offs')
                ->selectRaw('
                    customer_id,
                    customer_name,
                    group_id,
                    group_name,
                    customer_phone_number,
                    csa,
                    dda,
                    write_off_date,
                    principal_written_off,
                    interest_written_off,
                    principal_paid,
                    interest_paid')
                ->where('customer_name', 'like', '%' . $customer_id . '%')
                ->get();
        } else if ($search_by == 'group_id') {

            $customer_details = DB::table('written_offs')
                ->selectRaw('
                    customer_id,
                    customer_name,
                    group_id,
                    group_name,
                    customer_phone_number,
                    csa,
                    dda,
                    write_off_date,
                    principal_written_off,
                    interest_written_off,
                    principal_paid,
                    interest_paid')
                ->where('group_id', $customer_id)
                ->get();

        } else {
            return response()->json(['message' => 'Invalid search_by parameter'], 400);
        }

        return response()->json($customer_details, 200);
    }

    public function truncateWrittenOffs()
    {
        WrittenOff::truncate();
        return back()->with('success', 'Written offs truncated successfully.');
    }
}
