<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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

    public function onlineWrittenOffDetails(Request $request)
    {
        $searchPayload = $request->customer_id;
        $searchParam = $request->search_by;

        if ($searchParam == 'customer_id') {
            $searchCriteria = 'customerNo';
        } elseif ($searchParam == 'phone') {
            $searchCriteria = 'customerPhone';
        } elseif ($searchParam == 'name') {
            $searchCriteria = 'OfficerName';
        } elseif ($searchParam == 'group_id') {
            $searchCriteria = 'groupNo';
        } elseif ($searchParam == 'group_name') {
            $searchCriteria = 'groupName';
        } else {
            return response()->json(['message' => 'Invalid search_by parameter'], 400);
        }

        try {
            $onlineRequest = \Illuminate\Support\Facades\Http::timeout(90)->get('https://test.ug.vft24.org/crmapi/v1/loan/wof/' . $searchCriteria . '/' . $searchPayload);
        } catch (\Throwable $th) {
            \Illuminate\Support\Facades\Log::error('Error fetching written off details: ' . $th->getMessage());
            return response()->json(['status' => 'failed', 'message' => 'Unable to fetch written off details'], 500);
        }

        if ($onlineRequest->successful()) {
            $response = json_decode($onlineRequest->body(), true);
            if (isset($response['responseCode']) && $response['responseCode'] === '200') {
                return response()->json(['status' => 'success', 'data' => $response['data']], 200);
            } else {
                return response()->json(['status' => 'failed', 'message' => $response['responseMessage']], 500);
            }
        } else {
            return response()->json(['status' => 'failed', 'message' => 'Unable to fetch written off details'], 500);
        }
    }
}
