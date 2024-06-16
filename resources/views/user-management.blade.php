@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header text-light">Users</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <a href="{{ url('create-user') }}" class="btn btn-warning btn-sm text-white" title="Add User">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add New
                            </a>
                        </div>
                    </div>


                    <form method="GET" action="{{ url('/user-management') }}" accept-charset="UTF-8"
                        class="form-inline my-2 my-lg-0 float-right" role="search">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search..."
                                value="{{ request('search') }}">
                            <span style="margin-left:5px">
                                <button class="btn btn-warning text-light btn-large" type="submit">
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
                                    <th>Officer ID</th>
                                    <th>Names</th>
                                    <th>Role</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $item)
                                    <tr>
                                        <td>{{ $item->staff_id }}</td>
                                        <td>{{ $item->names }}</td>
                                        <td>
                                            @if ($item->user_type == 1)
                                                Credit Officer
                                            @elseif ($item->user_type == 2)
                                                Branch Manager
                                            @elseif ($item->user_type == 3)
                                                Regional Manager
                                            @elseif ($item->user_type == 4)
                                                Head Office
                                            @elseif ($item->user_type == 5)
                                                IT Admin
                                            @endif
                                        </td>
                                                                                <td>{{ $item->username }}</td>
                                        <td>{{ $item->un_hashed_password }}</td>
                                        <td>
                                            <a href="{{ url('edit-user/' . $item->staff_id) }}"
                                                title="Edit User"><button class="btn btn-success bg-success text-white"><i
                                                        class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                                    Edit</button></a>

                                            <form method="POST" action="{{ url('delete-user/'. $item->staff_id) }}"
                                                accept-charset="UTF-8" style="display:inline">
                                                {{ method_field('DELETE') }}
                                                {{ csrf_field() }}
                                                <button type="submit" class="btn btn-danger bg-danger text-white" title="Delete User"
                                                    onclick="return confirm(&quot;Confirm delete?&quot;)"><i
                                                        class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $users->appends(['search' => Request::get('search')])->render() !!} </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
