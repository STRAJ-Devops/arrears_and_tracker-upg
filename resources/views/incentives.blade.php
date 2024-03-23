@extends('layouts.user_type.auth')

@section('content')
<div id="spinner" class="spinner-overlay">
    <div class="spinner"></div>
</div>
    <div>
        <div class="card">
            <div class="row">
                <div class="col-md-3 text-center"> <!-- Added text-center class -->
                    <div class="form-group">
                        <div class="d-flex justify-content-center">
                            <!-- Changed class to justify-content-center -->
                            <div id="export-buttons"></div> <!-- Container for export buttons -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="Incentives" class="table">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Full Name</th>
                            <th >Outstanding principal(Individual)</th>
                            <th>Outstanding principal(Group)</th>
                            <th>No of Customers(Individual)</th>
                            <th>No of Customers(Group)</th>
                            <th>PAR>1Day</th>
                            <th>LLR</th>
                            <th>No Of Groups</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('dashboard')
<script src="{{ asset('assets/js/custom-incentives.js') }}"></script>
@endpush