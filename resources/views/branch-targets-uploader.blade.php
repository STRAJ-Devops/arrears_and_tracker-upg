@extends('layouts.user_type.auth')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header bg-warning text-light">Branch Targets</div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <a href="{{ url('upload-branch-targets') }}" class="btn btn-warning btn-sm text-light" title="Add Branch Targets">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add New
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ url('download-branch-targets-template') }}" class="btn btn-warning btn-block text-light" title="Add Branch Targets">
                            <i class="fa fa-download" aria-hidden="true"></i> Download Template
                        </a>
                    </div>
                </div>


                <form method="GET" action="{{ url('/branch-targets-uploader') }}" accept-charset="UTF-8"
                    class="form-inline my-2 my-lg-0 float-right" role="search">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search..."
                            value="{{ request('search') }}">
                        <span style="margin-left:5px">
                            <button class="btn btn-warning text-light" type="submit" style="height:34px">
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
                                <th>Branch ID</th>
                                <th>Branch Name</th>
                                <th>Target Amount</th>
                                <th>Target Numbers</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($targets as $item)
                                <tr>
                                    <td>{{ $item->branch_id }}</td>
                                    <td>{{ $item->branch->branch_name }}</td>
                                    <td>{{ $item->target_amount }}</td>
                                    <td>{{ $item->target_numbers }}</td>
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

@push('dashboard')
    <script>
        $(document).ready(function() {
            // Disable upload button initially
            $('#uploadBtn').prop('disabled', true);

            // Function to check if a file is selected
            $('#file').change(function() {
                if ($(this).val() !== '') {
                    $('#uploadBtn').prop('disabled', false); // Enable the upload button
                } else {
                    $('#uploadBtn').prop('disabled', true); // Disable the upload button
                }
            });

            // Handle form submission with AJAX
            $('#uploadForm').submit(function(event) {
                event.preventDefault();
                var formData = new FormData($(this)[0]);
                var progressBar = $('#progressBar');
                var overlay = $('#overlay');

                // Show overlay
                overlay.show();

                // Show progress bar
                $('#progress').show();

                // AJAX request
                $.ajax({
                    url: '/upload-branch-targets',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    xhr: function() {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(event) {
                            if (event.lengthComputable) {
                                var percentComplete = (event.loaded / event.total) *
                                    100;
                                progressBar.width(percentComplete + '%');
                                progressBar.attr('aria-valuenow', percentComplete);
                            }
                        }, false);
                        return xhr;
                    },
                    success: function(response) {
                        // Handle success
                        Swal.fire({
                            icon: 'success',
                            title: 'Upload Successful',
                            text: response.message,
                            showConfirmButton: false,
                            timer: 3000 // Close alert after 3 seconds
                        });

                        // Hide overlay
                        // overlay.hide();
                        // $('#uploadForm')[0].reset();
                        // $('#uploadBtn').prop('disabled',
                        //     true); // Disable the button after successful upload
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        var errorMessage = '';
                        if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON
                            .errors.branch_targets_file) {
                            errorMessage = xhr.responseJSON.errors.branch_targets_file[0];
                        } else {
                            errorMessage = 'An error occurred while uploading.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: errorMessage,
                            showConfirmButton: false,
                            timer: 3000 // Close alert after 3 seconds
                        });

                        // Hide overlay
                        overlay.hide();
                        $('#uploadForm')[0].reset();
                        $('#uploadBtn').prop('disabled',
                            true); // Disable the button after successful upload
                    }
                });

            });
        });
    </script>
@endpush




