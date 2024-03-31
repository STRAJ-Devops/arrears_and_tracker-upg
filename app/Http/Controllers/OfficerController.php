<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use Illuminate\Http\Request;

class OfficerController extends Controller
{
    public function index()
    {
        $officers = Officer::all();
        return view('user-management', compact('officers'));
    }

    public function getOfficers(Request $request)
    {
        // return response()->json([
        //     'data'=>$request->all()['draw']
        // ]);
        $columns = ['staff_id', 'names', 'username']; // Define your columns
        $orderBy = "staff_id"; // Set the default order column
        $orderDirection = "asc"; // Set the default order direction

        $users = Officer::select('*')
            ->orderBy($orderBy, $orderDirection)
            ->paginate($request->perPage);

        return response()->json([
            'data' => $users->items(), // Get the items instead of the paginator object
            'draw' => $request->all()['draw'], // Cast to integer
            'recordsTotal' => $users->total(),
            'recordsFiltered' => $users->total(),
        ]);
    }
}
