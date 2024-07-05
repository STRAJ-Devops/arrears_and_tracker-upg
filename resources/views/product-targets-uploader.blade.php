@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header bg-warning text-light">Product Targets</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <a href="{{ url('upload-product-targets') }}" class="btn btn-warning btn-sm text-light" title="Add New assignment">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add New
                            </a>
                        </div>

                        <div class="col">
                            <a href="{{ url('download-product-targets-template') }}" class="btn btn-warning btn-block text-light" title="Add Branch Targets">
                                <i class="fa fa-download" aria-hidden="true"></i> Download Template
                            </a>
                        </div>

                    </div>


                    <form method="GET" action="{{ url('/product-targets-uploader') }}" accept-charset="UTF-8"
                        class="form-inline my-2 my-lg-0 float-right" role="search">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search..."
                                value="{{ request('search') }}">
                            <span style="margin-left:5px">
                                <button class="btn btn-warning text-light" type="submit">
                                    Search
                                </button>
                            </span>
                        </div>
                    </form>

                    <br />
                    <br />
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product Name</th>
                                    <th>Target Amount</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($targets as $item)
                                    <tr>
                                        <td>{{ $item->product_id }}</td>
                                        <td>{{ $item->product->product_name }}</td>
                                        <td>{{ $item->target_amount }}</td>
                                        <td>{{ $item->created_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $targets->appends(['search' => Request::get('search')])->render() !!} </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.app')
