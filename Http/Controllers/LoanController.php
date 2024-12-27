<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $search_by = $request->search_by;
        $customer_id = $request->customer_id;
            $search_criteria = ($search_by == 'customer_id') ? 'customerNo' : (($search_by == 'loan_id') ? 'loanId' : (($search_by == 'officer_id') ? 'officerId' :  null));
            if ($search_criteria) {
                // $online_request = Http::get('https://test.ug.vft24.org/crmapi/v1/loan/customerNo/110129');
                $online_request = Http::get('https://test.ug.vft24.org/crmapi/v1/loan/'.$search_criteria.'/'.$customer_id);
                // $online_request = Http::get('https://test.ug.vft24.org/crmapi/v1/loan/arrears/'.$search_criteria.'/'.$customer_id);
            } else {
                return response()->json(['message' => 'Invalid search_by parameter'], 400);
            }
            if ($online_request->successful()) {
                $response_data = $online_request->json()['data'];

                return response()->json($response_data);
                // return response()->json(['customerName' => $response_data['customerName'], 'amountDisbursed' => $response_data['amountDisbursed'], 'outstandingPrincipal' => $response_data['outstandingPrincipal'], 'principalArrears' => $response_data['principalArrears'], 'interestArrears' => $response_data['interestArrears'], 'totalArrears' => $response_data['totalArrears'], 'noOfDaysLate' => $response_data['noOfDaysLate'], 'customerId' => $response_data['customerId']]);
            } else {
                return response()->json(['message' => 'Request failed'], 400);
            }
    }
}
