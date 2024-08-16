@extends('layouts.user_type.auth')
@section('content')
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Outstanding Principal</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['outstanding_principal'], 0, '.', ',') }}

                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">New Loans</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['total_disbursements'], 0, '.', ',') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-world text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Principal In Arrears</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['principal_arrears'], 0, '.', ',') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Interest In Arrears</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['outstanding_interest'], 0, '.', ',') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Number Of Women</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['number_of_female_borrowers'], 0, '.', ',') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Number Of Children</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['number_of_children'], 0, '.', ',') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Number Of Clients</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['number_of_clients'], 0, '.', ',') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Number Of Solidarity Groups</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['number_of_groups'], 0, '.', ',') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Number Of Solidarity Members</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['number_of_individuals'], 0, '.', ',') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">SGL</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ number_format($data['sgl'], 0, '.', ',') }}
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">PAR>1DAY</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ $data['par_1_days'] }}%
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mt-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">PAR>30DAYS</p>
                                <h5 class="font-weight-bolder mb-0 text-nowrap">
                                    {{ $data['par_30_days'] }}%
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mt-4 targets-div">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Portfolio Progress</p>
                                <div class="progress" style="height: 25px">
                                    <div class="progress-bar bg-gradient-warning progress-bar-custom" role="progressbar"
                                        style="width: {{ $data['officer_performance'] }}%;"
                                        aria-valuenow="{{ $data['officer_performance'] }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                        {{ $data['officer_performance'] }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-xl-3 col-sm-6 mt-4 targets-div">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Number of clients Progress</p>
                                <div class="progress" style="height: 25px">
                                    <div class="progress-bar bg-gradient-warning progress-bar-custom" role="progressbar"
                                        style="width: {{ $data['clients_performance'] }}%;"
                                        aria-valuenow="{{ $data['clients_performance'] }}" aria-valuemin="0"
                                        aria-valuemax="100">
                                        {{ $data['clients_performance'] }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-4">
            <div class="p-1" style="background-color:white;border-radius:25px;box-shadow:-3px 3px 1px #C2C2C2;">
                <canvas id="arrears-chart" width="300" height="150"></canvas>
            </div>
        </div>
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-4">
            <div class="p-1" style="background-color:white;border-radius:25px;box-shadow:-3px 3px 1px #C2C2C2;">
                <canvas id="targets-sales-chart" width="300" height="150"></canvas>
            </div>
        </div>
    </div>
    @if (auth()->user()->role == 5)
        <div class="row">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-4">
                <div class="p-1" style="background-color:white;border-radius:25px;box-shadow:-3px 3px 1px #C2C2C2;">
                    <canvas id="product-sales-targets" width="600" height="300"></canvas>
                </div>
            </div>
        </div>
    @endif
    @if (auth()->user()->role == 5)
        <div class="row">
            <div class="col-xl-12 col-sm-12 mb-xl-0 mb-4 mt-4">
                <div class="p-1" style="background-color:white;border-radius:25px;box-shadow:-3px 3px 1px #C2C2C2;">
                    <canvas id="branch-sales-targets" width="600" height="300"></canvas>
                </div>
            </div>
        </div>
    @endif
    @push('dashboard')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"
            integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <script>
            Chart.register(ChartDataLabels);

            var productLabels = {!! json_encode($data['product_labels']) !!};
            var productSales = {!! json_encode($data['product_sales']) !!};
            var productTargets = {!! json_encode($data['product_targets']) !!};
            var branchLabels = {!! json_encode($data['branch_labels']) !!};
            var branchSales = {!! json_encode($data['branch_sales']) !!};
            var branchTargets = {!! json_encode($data['branch_targets']) !!};
            var outstandingPrincipal = {!! json_encode($data['outstanding_principal']) !!};
            var PrincipalInArrears = {!! json_encode($data['principal_arrears']) !!};
            var totalTargets = {!! json_encode($data['total_targets']) !!};
            var totalSales = {!! json_encode($data['total_disbursements']) !!};

            var userRole = {!! json_encode(auth()->user()->role) !!};
        </script>
        <script src="{{ asset('assets/js/custom-dashboard.js?v=' . time()) }}"></script>
    @endpush
@endsection
