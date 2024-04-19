<?php

namespace App\Http\Controllers;

use App\Imports\ProductTargetsImport;
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
        $file = $request->file('product_targets_file');
        $file_name = time() . '_' . $file->getClientOriginalName();
        $save = $file->move(public_path('uploads'), $file_name);
        try {
            //truncate the ProductTarget table
           ProductTarget::truncate();

            $file = public_path('uploads/' . $file_name);
            $csv = array_map('str_getcsv', file($file));

            for ($i = 1; $i < count($csv); $i++) {
                $product_target = new ProductTarget();
                $product_target->product_id = $csv[$i][0];
                $product_target->target_amount = $csv[$i][2];
                $product_target->save();
            }
        } catch (\Exception $e) {
            // Return an error message if import fails
            return response()->json(['error' => 'Failed to import product targets. Please ensure the file format is correct.'], 400);
        }

        // Return a success message upon successful import
        return response()->json(['message' => 'Product targets imported successfully.'], 200);
    }
}
