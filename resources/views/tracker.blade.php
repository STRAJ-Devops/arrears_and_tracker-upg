@extends('layouts.user_type.auth')

@section('content')
    <div id="spinner" class="spinner-overlay">
        <div class="spinner"></div>
    </div>
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <div class="row card-header pb-0 d-flex justify-content-between">
                                    <div class="col-md-9">
                                        <div class="form-group d-flex">
                                            <label for="group-by" class="mr-2">Group By:</label>
                                            <select class="form-control" id="group-by">
                                                <option value="products">Product</option>
                                                <option value="branches-loans">Branch->Loan Disbursed</option>
                                                <option value="branches-clients">Branch->Clients</option>
                                                <option value="officers-loans">Officer->Loan Disbursed</option>
                                                <option value="officers-clients">Officer->Clients</option>
                                                <option value="regions-loans">Region->Loan Disbursed</option>
                                                <option value="regions-clients">Region->Clients</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center"> <!-- Added text-center class -->
                                        <div class="form-group">
                                            <div class="d-flex justify-content-center">
                                                <div id="export-buttons"></div> <!-- Container for export buttons -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table class="display responsive" id="branch-performance">
                                    <tbody>
                                    <tfoot align="right">
                                        <tr>
                                            <th colspan="2">Total</th>
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
        </div>
    </main>
    @push('dashboard')
        <script src="{{ asset('assets/js/custom-tracker.js') }}"></script>
    @endpush
@endsection
