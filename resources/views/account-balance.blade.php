@extends('layouts.user_type.auth')

@section('content')
    <div>
        <form action="" class="row g-3 align-items-center">
            <div class="col-1">
                <label for="search-by" class="col-form-label">Search By:</label>
            </div>
            <div class="col-2">
                <select class="form-select shadow-none" id="search-by" name="search-by">
                    <option value="customer_id">Customer ID</option>
                    <option value="phone">Phone</option>
                    <option value="name">Name</option>
                </select>
            </div>
            <div class="col-7">
                <input type="text" class="form-control shadow-none" id="search-customer">
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-outline-warning btn-block p-1 mt-3" id="search-button">Search</button>
            </div>
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
                $('#result-count').addClass('d-none'); // Hide result count initially

                setTimeout(function() {
                    $.ajax({
                        url: '/api/online-customer-details', // Adjust the URL as needed
                        type: 'POST',
                        data: {
                            customer_id: searchCustomerID,
                            search_by: $('#search-by').val()
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            // Hide spinner
                            $('#spinner').addClass('d-none');

                            if (response.status === 'success' && response.data.length > 0) {
                                $('#search-result').addClass('d-none');
                                $('#customer-details').removeClass('d-none');

                                // Show result count
                                $('#result-count').removeClass('d-none').find('p').text(
                                    `${response.data.length} result(s) found`);

                                // Clear previous customer details
                                $('#customer-details').empty();

                                // Log response to console
                                console.log(response.data);

                                // Loop through each customer detail and append it to the container
                                response.data.forEach(function(customer) {
                                    var customerCard = `
                                        <div class="card mt-3">
                                            <div class="card-header text-white" style="background-color: orange">
                                                Customer Details
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset('assets/img/avatar.png') }}" alt="Customer Avatar" 
                                                        class="rounded-circle me-3" width="80" height="80">
                                                    <div>
                                                        <h5 class="card-title">Customer Name: ${customer.customerName}</h5>
                                                        <p class="card-text">Customer ID: ${customer.customerId}</p>
                                                        <p class="card-text"><strong>Product:</strong> ${customer.product}</p>
                                                        <p class="card-text"><strong>Draw Down Balance:</strong> ${Number(customer.drawDownBalance).toLocaleString()} </p>
                                                        <p class="card-text"><strong>Compulsory Savings Account Balance:</strong> ${Number(customer.compSavingsBal).toLocaleString()} </p>
                                                        <p class="card-text"><strong>Loan Balance:</strong> ${Number(customer.loanBal).toLocaleString()} </p>
                                                        <p class="card-text"><strong>Amount Due Today:</strong> ${Number(customer.amountDueToday).toLocaleString()} </p>
                                                        <p class="card-text"><strong>Phone:</strong> ${(customer.phoneNo ?? 'N/A')}</p>
                                                        <p class="card-text"><strong>Group ID:</strong> ${customer.groupId ?? 'N/A'}</p>
                                                        <p class="card-text d-none">Outstanding Principal: ${Number(customer.outstandingPrincipal).toLocaleString()}</p>
                                                        <p class="card-text d-none">Outstanding Interest: ${Number(customer.outstandingInterest).toLocaleString()}</p>
                                                        <p class="card-text d-none">Principal Arrears: ${Number(customer.principalArrears).toLocaleString()}</p>
                                                        <p class="card-text d-none">Interest in Arrears: ${Number(customer.interestInArrears).toLocaleString()}</p>
                                                        <p class="card-text d-none">Number of Days Late: ${customer.numberOfDaysLate}</p>
                                                        <p class="card-text d-none">Number of Group Members: ${customer.numberOfGroupMembers}</p>
                                                        <p class="card-text d-none">Amount Disbursed: ${Number(customer.amountDisbursed).toLocaleString()}</p>
                                                        <p class="card-text d-none">Lending Type: ${customer.lendingType}</p>
                                                        <p class="card-text d-none">Gender: ${customer.gender}</p>
                                                        <p class="card-text d-none">Disbursement Date: ${customer.disbursementDate}</p>
                                                        <p class="card-text d-none">Staff: ${customer.staffId}</p>
                                                        <p class="card-text d-none">Branch: ${customer.branchId}</p>
                                                        <p class="card-text d-none">Product: ${customer.productId}</p>
                                                        <p class="card-text d-none">Village: ${customer.villageId}</p>
                                                        <p class="card-text d-none">PAR: ${customer.par}</p>
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
                                    `
                                    <p>Not Found</p>
                                    <p class="mt-3">No results found for "${searchCustomerID}"</p>
                                    `
                                );
                            }
                        },

                        error: function() {
                            // Hide spinner
                            $('#spinner').addClass('d-none');
                            $('#customer-details').addClass('d-none');
                            $('#search-result').removeClass('d-none').html(
                                `
                                <p>Not Found</p>
                                <p class="mt-3">An error occurred while searching for "${searchCustomerID}"</p>
                                `
                            );
                        }
                    });
                }, 5000); // Delay the AJAX call by 10 seconds
            });
        });
    </script>
@endpush
