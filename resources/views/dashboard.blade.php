@extends('layouts.user_type.auth')

@section('content')
    {{-- <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div id="piechart_3d" style=""></div>

                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Outstanding Principal</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($data['outstanding_principal'], 0, '.', ',') }}
                                    <span class="text-success text-sm font-weight-bolder">+55%</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
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
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($data['total_disbursements'], 0, '.', ',') }}
                                    <span class="text-success text-sm font-weight-bolder">+3%</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
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
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($data['principal_arrears'], 0, '.', ',') }}
                                    <span class="text-danger text-sm font-weight-bolder">-2%</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Number Of Women</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($data['number_of_female_borrowers'], 0, '.', ',') }}
                                    <span class="text-success text-sm font-weight-bolder">+5%</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
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
                                <h5 class="font-weight-bolder mb-0">
                                    {{ number_format($data['number_of_children'], 0, '.', ',') }}
                                    <span class="text-success text-sm font-weight-bolder">+5%</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
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
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $data['par_1_days'] }}%
                                    <span class="text-success text-sm font-weight-bolder">+5%</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
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
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $data['par_30_days'] }}%
                                    <span class="text-success text-sm font-weight-bolder">+5%</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('dashboard')
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load("current", {
                packages: ["corechart"]
            });
            google.charts.setOnLoadCallback(drawChart);

            function drawChart() {
                var pie_data = <?php echo json_encode($data['pie_array']); ?>;

                var data = google.visualization.arrayToDataTable([
                    ['Task', 'Hours per Day'],
                    ['Principal Arrears', pie_data.principal_arrears],
                    ['Outstanding Principal', pie_data.outstanding_principal]
                ]);

                var options = {
                    title: 'Outstanding and Principal Arrears',
                    is3D: true,
                };

                var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
                chart.draw(data, options);
            }
        </script>
    @endpush
@endsection
