@extends('layouts.user_type.auth')

@section('content')
    <div>
        <form action="" class="d-flex">
            <input type="text" class="form-control shadow-none me-2" id="search-customer"
                placeholder="Enter Customer ID e.g 123456 or phone eg. 785065399 or full name">
            <button type="button" class="btn btn-outline-primary" id="search-button">Search</button>
        </form>
    </div>

    <!-- Show the number of results -->
    <div id="result-count" class="mt-3 d-none">
        <p></p>
    </div>

    <div id="search-result" class="mt-3">
        <p>Not Found</p>
    </div>

    <div id="spinner" class="spinner-border text-primary d-none" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>

    <div id="customer-details" class="card mt-3 d-none">
        <div class="card-header text-white" style="background-color: orange">
            Group Details
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
                $('#result-count').addClass('d-none'); // Hide result count initially

                setTimeout(function() {
                    $.ajax({
                        url: 'get-group-details', // Adjust the URL as needed
                        type: 'GET',
                        data: {
                            customer_id: searchCustomerID,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Hide spinner
                            $('#spinner').addClass('d-none');

                            if (response.length > 0) {
                                $('#search-result').addClass('d-none');
                                $('#customer-details').removeClass('d-none');

                                // Show result count
                                $('#result-count').removeClass('d-none').find('p').text(
                                    `${response.length} result(s) found`);

                                // Clear previous customer details
                                $('#customer-details').empty();

                                // Loop through each customer detail and append it to the container
                                response.forEach(function(customer) {
                                    var customerCard = `
                <div class="card mt-3">
                    <div class="card-header text-white" style="background-color: orange">
                        Group Member
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/img/avatar.png') }}" alt="Customer Avatar" class="rounded-circle me-3" width="80" height="80">
                            <div>
                                <h5 class="card-title">Customer Name: ${customer.names}</h5>
                                <p class="card-text"><strong>Customer ID: </strong> ${Number(customer.customer_id)}</p>
                                <p class="card-text"><strong>Phone Number:</strong> ${Number(customer.phone)}</p>
                                <p class="card-text"><strong>Group ID:</strong> ${customer.group_id}</p>
                                <p class="card-text"><strong>Group Name:</strong> ${customer.group_name}</p>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                                    $('#customer-details').append(customerCard);
                                });
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
