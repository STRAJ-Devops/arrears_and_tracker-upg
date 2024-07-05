@extends('layouts.user_type.auth')

@section('content')
    <div id="spinner" class="spinner-overlay">
        <div class="spinner"></div>
    </div>
    <div class="alert alert-warning text-light font-weight-bold lead text-center" role="alert">
        Today's Expected repayments
      </div>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div id="export-buttons-branch-performance"></div> <!-- Container for export buttons -->
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <div class="row card-header pb-0 d-flex justify-content-between">
                            @if (Auth::user()->user_type == 5 || Auth::user()->user_type ==4 || Auth::user()->user_type ==3 || Auth::user()->user_type ==2)
                                <div class="col-md-9">
                                    <div class="form-group d-flex">
                                        <label for="staff" class="mr-2">Group By</label>
                                        <select class="form-control staff shadow-none" id="staff">
                                            <option value="staff_id">Officer</option>
                                            <option value="branch_id">Branch</option>
                                            <option value="region_id">Region</option>
                                            <option value="loan_product">Loan Product</option>
                                            <option value="gender">Gender</option>
                                            <option value="district">District</option>
                                            <option value="sub_county">Sub County</option>
                                            <option value="age">Age</option>
                                            <option value="client">client</option>
                                        </select>
                                    </div>
                                </div>
                            @endif

                            @if (Auth::user()->user_type == 1)
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="date-filter">Select Date</label>
                                        <input type="text" class="form-control" id="date-filter">
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-3 text-center"> <!-- Added text-center class -->
                                <div class="form-group">
                                    <div class="d-flex justify-content-center">
                                        <!-- Changed class to justify-content-center -->
                                        <div id="export-buttons"></div> <!-- Container for export buttons -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table id="expected-repayments-table" class="display responsive" style="width:100%">
                            <tbody>
                            <tfoot align="right">
                                <tr>
                                    <th colspan="1">Over all total</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('dashboard')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script> <!-- Include jQuery UI -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css"> <!-- jQuery UI CSS -->
        <script src="{{ asset('assets/js/custom-expected-repayments.js') }}"></script>
    @endpush
@endsection
