@extends('layouts.user_type.auth')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-body px-4 pt-4 pb-2">
                        <h2 class="text-center mb-4">Upload Sales and Arrears</h2>
                        <form id="uploadForm" enctype="multipart/form-data" method="post">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label">Choose File:</label>
                                <input type="file" name="upload_template_file" id="file" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary btn-block">Upload</button>
                                </div>

                                <div class="col">
                                    <a href="{{ url('truncate-arrears-and-sales') }}"
                                        class="btn btn-danger btn-block text-light" title="Add Branch Targets">
                                        <i class="fa fa-trash" aria-hidden="true"></i> Truncate
                                    </a>
                                </div>
                            </div>
                        </form>
                        <div id="progress" class="progress mt-3" style="display: none;">
                            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;"
                                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('dashboard')
    <style>
        .main-content {
            height: 100vh;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #4caf50;
            border-color: #4caf50;
        }

        .btn-primary:hover {
            background-color: #43a047;
            border-color: #43a047;
        }

        .progress-bar {
            background-color: #4caf50;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

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
                    url: '/upload-sales-targets',
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
                        overlay.hide();
                        $('#uploadForm')[0].reset();
                        $('#uploadBtn').prop('disabled',
                            true); // Disable the button after successful upload
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
