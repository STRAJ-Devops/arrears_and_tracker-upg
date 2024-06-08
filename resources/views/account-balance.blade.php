@extends('layouts.user_type.auth')

@section('content')
    <div>
        <form action="" class="d-flex">
            <input type="text" class="form-control shadow-none me-2" id="search-customer" placeholder="Enter Customer ID e.g 123456">
            <button type="button" class="btn btn-outline-primary" id="search-button">Search</button>
        </form>
    </div>

    <div id="search-result" class="mt-3">
        <p>Not Found</p>
    </div>

    <div id="customer-details" class="card mt-3 d-none">
        <div class="card-header text-white" style="background-color: orange">
            Customer Details
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center">
                <img src="{{ asset('assets/img/avatar.png')}}" alt="Customer Avatar" id="customer-avatar" class="rounded-circle me-3" width="80" height="80">
                <div>
                    <h5 class="card-title" id="customer-name">Customer Name: John Doe</h5>
                    <p class="card-text"><strong>Draw Down Balance:</strong> <span id="draw-down-balance">$1,000.00</span></p>
                    <p class="card-text"><strong>Compulsory Savings Account Balance:</strong> <span id="savings-balance">$5,000.00</span></p>
                    <p class="card-text"><strong>Loan Balance:</strong> <span id="loan-balance">$2,500.00</span></p>
                    <p class="card-text"><strong>Amount Due Today:</strong> <span id="amount-due">$150.00</span></p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('dashboard')
<script>
    $(document).ready(function() {
        $('#search-button').click(function() {
            // Example customer data for demonstration
            // image path from assets

            const avatar = "{{ asset('assets/img/avatar.png')}}"
            console.log(avatar);
            const customerData = {
                id: '123456',
                name: 'John Doe',
                avatar: avatar, // Example avatar URL
                drawDownBalance: '$1,000.00',
                savingsBalance: '$5,000.00',
                loanBalance: '$2,500.00',
                amountDue: '$150.00'
            };

            const searchCustomerID = $('#search-customer').val();

            if (searchCustomerID === customerData.id) {
                $('#search-result').addClass('d-none');
                $('#customer-details').removeClass('d-none');
                $('#customer-name').text(`Customer Name: ${customerData.name}`);
                $('#customer-avatar').attr('src', avatar);
                //set the width and height of the avatar
                $('#customer-avatar').attr('width', "30%");
                $('#customer-avatar').attr('height', "30%");
                $('#draw-down-balance').text(customerData.drawDownBalance);
                $('#savings-balance').text(customerData.savingsBalance);
                $('#loan-balance').text(customerData.loanBalance);
                $('#amount-due').text(customerData.amountDue);
            } else {
                $('#customer-details').addClass('d-none');
                $('#search-result').removeClass('d-none').html('<p>Not Found</p>');
            }
        });
    });
</script>
@endpush
