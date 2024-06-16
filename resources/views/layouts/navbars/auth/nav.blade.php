<!-- Navbar -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3 text-nowrap">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/dashboard">Home</a></li>
                <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
                    {{ str_replace('-', ' ', Request::path()) }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize">{{ str_replace('-', ' ', Request::path()) }}</h6>
        </nav>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            @php
                                $currentTime = date('H:i'); // Get current time in 24-hour format
                                $greeting = ''; // Initialize an empty string for the greeting

                                // Determine the appropriate greeting based on the time of the day
                                if ($currentTime >= '12:00' && $currentTime < '18:00') {
                                    $greeting = 'Good Afternoon';
                                } elseif ($currentTime >= '18:00' && $currentTime < '24:00') {
                                    $greeting = 'Good Evening';
                                } else {
                                    $greeting = 'Good Morning';
                                }
                            @endphp
                            <h1 style="font-size: 4vw;">{{ $greeting . ', ' . auth()->user()->names }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar">
            <ul class="navbar-nav  justify-content-end">
                <li class="nav-item d-flex align-items-center pe-3">
                    <a href="{{ url('/logout') }}" class="nav-link text-body font-weight-bold px-0">
                        <i class="fa fa-user me-sm-1"></i>
                        <span class="d-sm-inline d-none">Sign Out</span>
                    </a>
                </li>
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>

                <li class="nav-item dropdown pe-2 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex flex-column m-3">
                            <img src="../assets/img/avatar.png" class="avatar avatar-sm  me-3 ">
                            <h6 class="text-sm font-weight-normal">{{ auth()->user()->names }}</h6>
                        </div>
                    </a>

                </li>
            </ul>
        </div>
    </div>
</nav>
<!-- End Navbar -->
