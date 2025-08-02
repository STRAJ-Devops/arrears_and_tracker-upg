@extends('layouts.user_type.guest')

@section('content')
<!-- change password blade -->
<div class="page-header section-height-75">
    <div class="container">
        <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
                <div class="card card-plain mt-8">
                    <div class="card-header pb-0 text-left bg-transparent">
                        <h4 class="mb-0">Change Password</h4>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                        <p class="text-danger text-xs">{{ session('error') }}</p>
                        @endif

                        <form method="POST" action="{{ route('force-password-change.submit') }}">
                            @csrf

                            <div>
                                <label for="password">New Password</label>
                                <div>
                                    <input id="password" name="password" type="password" class="form-control" placeholder="New Password" aria-label="Password">
                                    @error('password')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="password_confirmation">Confirm Password</label>
                                <div>
                                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" placeholder="Confirm Password" aria-label="Password Confirmation">
                                    @error('password_confirmation')
                                    <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn bg-warning w-100 mt-4 mb-0">Update Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                    <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image:url('../assets/img/curved-images/curved6.jpg')"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection