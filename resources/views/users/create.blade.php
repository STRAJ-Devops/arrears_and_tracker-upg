@extends('layouts.user_type.auth')

@section('content')
<div class="content-wrapper">
    <div class="row">
      <div class="col-sm-12">
                <div class="card">
                    <div class="card-header text-light">Add New User</div>
                    <div class="card-body">
                        <a href="{{ url('user-management') }}" title="Back"><button class="button2"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />
                        <form method="POST" action="{{ url('/store-user') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            @include ('users.form', ['formMode' => 'create'])

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection