@extends('layouts.user_type.guest')

@section('content')

  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-75">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
              <div class="card mt-8">
                <div class="card-header pb-0 text-left bg-transparent">
                  <img src="{{asset('assets/img/vfu_logo.png')}}" alt="vfu_logo" width="80%" height="80%" class="img-fluid">
                  <p class="mb-0">Staff Login<br></p>
                </div>
                <div class="card-body">
                  <form role="form" method="POST" action="/session">
                    @csrf
                    <label class="text-dark h5">Username</label>
                    <div class="mb-3">
                      <input type="text" class="form-control shadow-none" name="username" id="username" placeholder="Username" aria-label="Email" aria-describedby="email-addon">
                      @error('email')
                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                      @enderror
                    </div>
                    <label class="text-dark h5">Password</label>
                    <div class="mb-3">
                      <input type="password" class="form-control shadow-none" name="password" id="password" placeholder="Password" aria-label="Password" aria-describedby="password-addon">
                      @error('password')
                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                      @enderror
                    </div>
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="rememberMe" checked="">
                      <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn bg-warning w-100 mt-4 mb-0 text-light">Sign in</button>
                    </div>
                  </form>
                </div>

              </div>
            </div>
            <div class="col-md-6">
              <div class="oblique position-absolute top-0 h-100 d-md-block d-none me-n8">
                <div class="oblique-image bg-cover position-absolute fixed-top ms-auto h-100 z-index-0 ms-n6" style="background-image:url('../assets/img/vulnerable.jpg')"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

@endsection
