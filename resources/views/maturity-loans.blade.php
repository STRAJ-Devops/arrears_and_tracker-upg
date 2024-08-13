@extends('layouts.user_type.auth')

@section('content')
    <div id="spinner" class="spinner-overlay">
        <div class="spinner"></div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div id="export-buttons-branch-performance"></div> <!-- Container for export buttons -->
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="filters p-3">
                        <div class="row">
                            {{-- filter by heading --}}
                            <div class="col-12">
                                <h5 class="mb-0">Filter By</h5>
                            </div>
                            <div class="col-md-3">
                                
                                <select id="branchFilter" class="form-control shadow-none">
                                    <option value="">All Branches</option>
                                    <!-- Populate dynamically -->
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select id="productFilter" class="form-control">
                                    <option value="">All Products</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="text" id="clientIdFilter" class="form-control shadow-none" placeholder="Client ID">
                            </div>
                            <div class="col-md-2">
                                <input type="text" id="phoneFilter" class="form-control shadow-none" placeholder="Phone Number">
                            </div>
                            <div class="col-md-2">
                                <input type="text" id="maturityDateFilter" class="form-control datepicker shadow-none" placeholder="Maturity Date">
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive p-0">
                        <table id="maturity-loans" class="display responsive" style="width:100%">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('dashboard')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="{{ asset('assets/js/custom-maturity-loans.js') }}"></script>
    @endpush
@endsection
