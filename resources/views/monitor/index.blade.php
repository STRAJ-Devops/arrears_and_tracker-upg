@extends('layouts.user_type.auth')

@section('content')
    <div class="d-flex justify-content-between">
        <h6 class="fs-1">Sales Activity Monitor</h1>
            <div>
                <div class="form-group">
                    <label for="date-filter">Select Date</label>
                    <input type="text" class="form-control" id="date-filter">
                </div>
            </div>
            <div>
                <a href="{{ route('create-monitor') }}" class="btn btn-warning">Add</a>

            </div>
    </div>
    <div class="container-fluid ms-5 ps-5">
        <ul class="nav nav-underline nav-fill fixed-bottom custom-navbar" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fs-4" id="all" data-bs-toggle="pill" data-bs-target="#all"
                    type="button" role="tab" aria-controls="all" aria-selected="true">All</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fs-4" id="Marketing" data-bs-toggle="pill" data-bs-target="#Marketing"
                    type="button" role="tab" aria-controls="Marketing" aria-selected="false">Marketing</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fs-4" id="Appraisal" data-bs-toggle="pill" data-bs-target="#Appraisal"
                    type="button" role="tab" aria-controls="Appraisal" aria-selected="false">Appraisal</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fs-4" id="Application" data-bs-toggle="pill" data-bs-target="#Application"
                    type="button" role="tab" aria-controls="Application" aria-selected="false">Application</button>
            </li>
        </ul>
    </div>


    <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all">
            <div id="spinner" class="spinner-overlay">
                <div class="spinner"></div>
            </div>
            <table id="monitor-table" class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Activity</th>
                        <th>Marketing Date</th>
                        <th>Appraisal Date</th>
                        <th>Application Date</th>
                        <th>Officer</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade show" id="Marketing" role="tabpanel" aria-labelledby="Marketing">
            <div id="spinner" class="spinner-overlay">
                <div class="spinner"></div>
            </div>
            <table id="monitor-table " class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Activity</th>
                        <th>Date</th>
                        <th>Officer</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="tab-pane show fade" id="Appraisal" role="tabpanel" aria-labelledby="Appraisal">
            <div id="spinner" class="spinner-overlay">
                <div class="spinner"></div>
            </div>
            <table id="monitor-table" class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Activity</th>
                        <th>Date</th>
                        <th>Officer</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="tab-pane fade" id="Application" role="tabpanel" aria-labelledby="Application">
            <div id="spinner" class="spinner-overlay">
                <div class="spinner"></div>
            </div>
            <table id="monitor-table" class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Activity</th>
                        <th>Date</th>
                        <th>Officer</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    @push('dashboard')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script> <!-- Include jQuery UI -->
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css"> <!-- jQuery UI CSS -->
        <script src="{{ asset('assets/js/custom-monitor.js') }}"></script>
    @endpush
    {{-- tab contents --}}
@endsection
