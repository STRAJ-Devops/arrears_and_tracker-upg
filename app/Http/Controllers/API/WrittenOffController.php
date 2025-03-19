<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\WriteOffCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WrittenOffController extends Controller
{

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

    public function online_customer(Request $request)
    {
        $search_payload = $request->customer_id;
        $search_by = $request->search_by;
        if ($search_by == 'customer_id') {
            $search_criteria = 'customerNo';
        } elseif ($search_by == 'officer_name') {
            $search_criteria = 'officerName';
        } elseif ($search_by == 'group_name') {
            $search_criteria = 'groupName';
        } elseif ($search_by == 'group_id') {
            $search_criteria = 'groupNo';
        } elseif ($search_by == 'phone') {
            $search_criteria = 'phoneNumber';
        } else {
            $search_criteria = 'customerNo';
        }

        try {
            $online_request = Http::timeout(90)->get('https://test.ug.vft24.org/crmapi/v1/loan/wof/'.$search_criteria.'/'.$search_payload);
        } catch (\Throwable $th) {
            $online_request = null;
        }


        if ($online_request && $online_request->successful() && $online_request->json('data')) {
            $data = $online_request->json('data');
            WriteOffCache::updateOrCreate(
                ['param' => $search_criteria, 'key' => $search_payload],
                ['data' => $data]
            );
            return response()->json($data);
        } else {
            $cache = WriteOffCache::where('param', $search_criteria)
            ->where('key', $search_payload)
            ->latest()
            ->first()?->data;
            if ($cache) {
                return response()->json($cache);
            } else {
                return response()->json("Not found - ".$search_criteria, 400);
            }
        }
    }
}
