<?php

namespace App\Http\Controllers;

use App\Imports\ProductTargetsImport;
use App\Models\Product;
use App\Models\ProductTarget;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductTargetController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 25;
        if (!empty($keyword)) {
            $targets = ProductTarget::with('product')
                ->whereHas('product', function ($query) use ($keyword) {
                    $query->where('product_name', 'LIKE', "%$keyword%");
                })
                ->orWhere('product_id', 'LIKE', "%$keyword%")
                ->paginate($perPage);
        } else {
            $targets = ProductTarget::with('product')->paginate($perPage);
        }
        return view('product-targets-uploader', compact('targets'));
    }

    public function uploadProductTargets()
    {
        return view('upload-product-targets');
    }

    public function deleteProductTargets()
    {
        //empty the BranchTarget table
        $delete = ProductTarget::truncate();
        if (!$delete) {
            return response()->json(['error' => 'Failed to delete producttargets. Please try again.'], 400);
        }
        return redirect()->back()->with('success', 'Product targets deleted successfully.');
    }

    public function import(Request $request)
    {
        //truncate the ProductTarget table
        ProductTarget::truncate();
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
