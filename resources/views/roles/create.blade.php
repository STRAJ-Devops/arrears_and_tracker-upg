@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Create New Role</h2>
                <a class="btn btn-warning" href="{{ route('roles.index') }}">Back</a>
            </div>
        </div>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-12 mb-3">
                <div class="form-group">
                    <label for="name" class="form-label"><strong>Name:</strong></label>
                    <input type="text" name="name" class="form-control shadow-none" value="{{ old('name') }}" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <div class="form-group">
                    <label class="form-label"><strong>Permission:</strong></label>

                    <div class="form-check">
                        <div class="row">
                        @foreach ($permission as $value)
                            <div class="mb-2 col-3">
                                <input type="checkbox" name="permission[]" value="{{ $value->id }}" class="form-check-input" id="permission{{ $value->id }}">
                                <label class="form-check-label" for="permission{{ $value->id }}">
                                    {{ $value->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-warning">Submit</button>
            </div>
        </div>
    </form>
@endsection
