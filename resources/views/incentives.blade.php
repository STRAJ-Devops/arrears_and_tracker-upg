@extends('layouts.user_type.auth')

@section('content')
    <div id="spinner" class="spinner-overlay">
        <div class="spinner"></div>
    </div>
    <div id="general-section">
        <p>Loading data....................</p>
    </div>
    <div id="table-section" style="display: none;"> <!-- Initially hide this section -->
        <nav class="">
            <div class="nav nav-tabs nav-fill nav-underline" id="nav-tab" role="tablist">
                <button class="nav-link active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general"
                    type="button" role="tab" aria-controls="general" aria-selected="true">General</button>
                <button class="nav-link" id="individual-tab" data-bs-toggle="pill" data-bs-target="#individual"
                    type="button" role="tab" aria-controls="individual" aria-selected="false">Individual</button>
                <button class="nav-link" id="groups-tab" data-bs-toggle="pill" data-bs-target="#groups" type="button"
                    role="tab" aria-controls="groups" aria-selected="false">Groups</button>
                <button class="nav-link" id="fast-tab" data-bs-toggle="pill" data-bs-target="#fast" type="button"
                    role="tab" aria-controls="fast" aria-selected="false">Fast</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab"
                tabindex="0">
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

                    <div class="table-responsive incentives-table">
                        <table id="Incentives-general" class="table">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Full Name</th>
                                    <th>Outstanding principal(Individual)</th>
                                    <th>Outstanding principal(Group)</th>
                                    <th>Outstanding principal(SGL)</th>
                                    <th>No of Customers(Individual)</th>
                                    <th>No of Customers(Group)</th>
                                    <th>No of SGL Groups</th>
                                    <th>PAR>1Day</th>
                                    <th>LLR</th>
                                    <th>SGL</th>
                                    <th>Incentive(PAR>1Day)</th>
                                    <th>Incentive(Net Portifolio Growth)</th>
                                    <th>Incentive(Net Client Growth)</th>
                                    <th>Incentive(No of SGL Groups)</th>
                                    <th>Total Incentive</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="individual" role="tabpanel" aria-labelledby="individual-tab" tabindex="0">
                <table id="Incentives-individual" class="table">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Full Name</th>
                            <th>Outstanding principal(Individual)</th>
                            <th>No of Customers(Individual)</th>
                            <th>PAR>1Day</th>
                            <th>LLR</th>
                            <th>Incentive(PAR>1Day)</th>
                            <th>Incentive(Net Portifolio Growth)</th>
                            <th>Incentive(Net Client Growth)</th>
                            <th>Total Incentive</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="groups" role="tabpanel" aria-labelledby="groups-tab" tabindex="0">
                <table id="Incentives-groups" class="table">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Full Name</th>
                            <th>Outstanding principal(Group)</th>
                            <th>No of Customers(Group)</th>
                            <th>PAR>1Day</th>
                            <th>LLR</th>
                            <th>Incentive(PAR>1Day)</th>
                            <th>Incentive(Net Portifolio Growth)</th>
                            <th>Incentive(Net Client Growth)</th>
                            <th>Total Incentive</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="fast" role="tabpanel" aria-labelledby="fast-tab" tabindex="0">
                <table id="Incentives-fast" class="table">
                    <thead>
                        <tr>
                            <th>Staff ID</th>
                            <th>Full Name</th>
                            <th>Outstanding principal(SGL)</th>
                            <th>PAR>1Day</th>
                            <th>LLR</th>
                            <th>SGL</th>
                            <th>Incentive(PAR>1Day)</th>
                            <th>Incentive(Net Portifolio Growth)</th>
                            <th>Incentive(Net Client Growth)</th>
                            <th>Incentive(No of SGL Groups)</th>
                            <th>Total Incentive</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>


    </div>

    <div id="incentives-card-section" style="display: none;"> <!-- Initially hide this section -->
        <div class="congrats-bg">
            <div class="balloons">
                @for ($i = 1; $i <= 50; $i++)
                    <div class="balloon balloon{{ $i }}"></div>
                @endfor
            </div>
        </div>

    </div>
@endsection

@push('dashboard')
    <style>
        .congrats-bg {
            width: 100%;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: -1;
        }

        .balloons {
            position: absolute;
            transform: translate(-50%, -50%);
        }

        .balloon {
            width: 30px;
            height: 40px;
            background-color: #f1c40f;
            border-radius: 50%;
            position: absolute;
            animation: balloonAnimation 5s ease infinite;
        }

        @for ($i = 1; $i <= 50; $i++)
            .balloon{{ $i }} {
                top: calc(100vh * {{ rand(0, 100) / 100 }});
                /* Randomize top position */
                left: calc(100vw * {{ rand(0, 100) / 100 }});
                /* Randomize left position */
                animation-duration: {{ rand(5, 10) }}s;
                /* Randomize animation duration */
            }
        @endfor

        @keyframes balloonAnimation {
            0% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-100px) scale(1.2);
            }

            100% {
                transform: translateY(0) scale(1);
            }
        }
    </style>
    <script>
        var logged_user = {!! json_encode($logged_user) !!};
    </script>
    <script src="{{ asset('assets/js/custom-incentives.js') }}"></script>
@endpush
