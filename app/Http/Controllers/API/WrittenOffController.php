<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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
        } elseif ($search_by == 'contract') {
            $search_criteria = 'contractNo';
        } elseif ($search_by == 'officer') {
            $search_criteria = 'officerNo';
        } else {
            $search_criteria = 'customerNo';
        }

        $online_request = Http::get('https://test.ug.vft24.org/crmapi/v1/loan/wof/'.$search_criteria.'/'.$search_payload);

        if ($online_request->successful()) {
            return response()->json($online_request->json()['data']);
        } else {
            return response()->json("Not found - ".$search_criteria, 400);
        }
    }
}
