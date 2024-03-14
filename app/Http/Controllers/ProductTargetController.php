<?php

namespace App\Http\Controllers;

use App\Imports\ProductTargetsImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductTargetController extends Controller
{
    public function index()
    {
        return view('product-targets-uploader');
    }
    public function import(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'product_targets_file' => 'required|mimes:xlsx,xls,csv'
        ], [
            'product_targets_file.required' => 'Please upload a file.',
            'product_targets_file.mimes' => 'The uploaded file must be a valid Excel or CSV file.'
        ]);

        try {
            // Import the file using BranchTargetsImport class
            Excel::import(new ProductTargetsImport, $request->file('product_targets_file'));
        } catch (\Exception $e) {
            // Return an error message if import fails
            return response()->json(['error' => 'Failed to import product targets. Please ensure the file format is correct.'], 400);
        }

        // Return a success message upon successful import
        return response()->json(['message' => 'Product targets imported successfully.'], 200);
    }
}
