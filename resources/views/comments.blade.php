@extends('layouts.user_type.auth')

@section('content')
    <div id="spinner" class="spinner-overlay">
        <div class="spinner"></div>
    </div>
    <div>
        <div class="card">
            <div class="table-responsive">
                <table id="comments-table" class="table">
                    <thead>
                        <tr>
                            <th>Comment ID</th>
                            <th>Customer ID</th>
                            <th>Customer Name</th>
                            <th>Comments</th>
                            <th>Number Of Days Late</th>
                            <th>Date Posted</th>
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
    <script src="{{ asset('assets/js/custom-comments.js') }}"></script>
@endpush
