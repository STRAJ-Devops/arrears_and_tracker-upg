@extends('layouts.user_type.auth')

@section('content')
<main class="main-content d-flex justify-content-center align-items-center">
    <div class="container">
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-body px-4 pt-4 pb-2">
                        <h2 class="text-center mb-4">Upload Product Targets</h2>
                        <form id="uploadForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="product_targets_file" class="form-label">Choose File:</label>
                                <input type="file" name="product_targets_file" id="file" class="form-control">
                            </div>
                            <button type="submit" id="uploadBtn" class="btn btn-warning btn-lg"
                                disabled>Upload</button>
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
</main>
@endsection

@push('dashboard')
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
                    url: '/upload-product-targets',
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

                            //navigate to product-targets-uploader using jquery
                            window.location.href = '/product-targets-uploader';
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