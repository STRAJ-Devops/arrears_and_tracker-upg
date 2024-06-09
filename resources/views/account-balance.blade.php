@extends('layouts.user_type.auth')

@section('content')
    <div>
        <form action="" class="d-flex">
            <input type="text" class="form-control shadow-none me-2" id="search-customer"
                placeholder="Enter Customer ID e.g 123456">
            <button type="button" class="btn btn-outline-primary" id="search-button">Search</button>
        </form>
    </div>

    <div id="search-result" class="mt-3">
        <p>Not Found</p>
    </div>

    <div id="spinner" class="spinner-border text-primary d-none" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>

    <div id="customer-details" class="card mt-3 d-none">
        <div class="card-header text-white" style="background-color: orange">
            Customer Details
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/avatar.png') }}" alt="Customer Avatar" id="customer-avatar"
                    class="rounded-circle me-3" width="80" height="80">
                <div>
                    <h5 class="card-title" id="customer-name">Customer Name: John Doe</h5>
                    <p class="card-text"><strong>Draw Down Balance:</strong> <span id="draw-down-balance">$1,000.00</span>
                    </p>
                    <p class="card-text"><strong>Compulsory Savings Account Balance:</strong> <span
                            id="savings-balance">$5,000.00</span></p>
                    <p class="card-text"><strong>Loan Balance:</strong> <span id="loan-balance">$2,500.00</span></p>
                    <p class="card-text"><strong>Amount Due Today:</strong> <span id="amount-due">$150.00</span></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('dashboard')
    <style>
        .spinner-border {
            width: 3rem;
            height: 3rem;
            margin: 0 auto;
            display: block;
            color: orange;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#search-button').click(function() {
                const searchCustomerID = $('#search-customer').val();

                // Show spinner
                $('#spinner').removeClass('d-none');
                $('#customer-details').addClass('d-none');
                $('#search-result').addClass('d-none');

                setTimeout(function() {
                    $.ajax({
                        url: 'customer-details', // Adjust the URL as needed
                        type: 'GET',
                        data: {
                            customer_id: searchCustomerID
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Hide spinner
                            $('#spinner').addClass('d-none');
                            if (response) {
                                $('#search-result').addClass('d-none');
                                $('#customer-details').removeClass('d-none');
                                $('#customer-name').text(
                                    `Customer Name: ${response.names}`);
                                $('#customer-avatar').attr('src',
                                    "{{ asset('assets/img/avatar.png') }}");
                                //set the width and height of the avatar
                                $('#customer-avatar').attr('width', "30%");
                                $('#customer-avatar').attr('height', "30%");
                                $('#draw-down-balance').text(response
                                    .draw_down_balance + '/=');
                                $('#savings-balance').text(response.savings_balance +
                                    '/=');
                                $('#loan-balance').text(response.loan_balance + '/=');
                                $('#amount-due').text(response.amount_due + '/=');
                            } else {
                                $('#customer-details').addClass('d-none');
                                $('#search-result').removeClass('d-none').html(
                                    '<p>Not Found</p>');
                            }
                        },
                        error: function() {
                            // Hide spinner
                            $('#spinner').addClass('d-none');
                            $('#customer-details').addClass('d-none');
                            $('#search-result').removeClass('d-none').html(
                                '<p>Not Found</p>');
                        }
                    });
                }, 5000); // Delay the AJAX call by 10 seconds
            });
        });
    </script>
@endpush
