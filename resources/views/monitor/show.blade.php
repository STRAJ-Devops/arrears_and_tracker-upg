@extends('layouts.user_type.auth')

@section('content')
    <div class="content-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header bg-warning text-light font-weight-bold">{{ $monitor->name }}'s Details</div>

                    <div class="card-body">
                        <a href="{{ url('/monitors') }}" title="Back"><button class="button2"><i class="fa fa-arrow-left"
                                    aria-hidden="true"></i> Back</button></a>
                        <br />
                        <br />

                        {{-- activity details --}}
                        <div class="container">
                            <div class="row">
                                <div class="col">
                                    <p><strong>Location:</strong> {{ $monitor->location }}</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <p><strong>Phone:</strong> {{ $monitor->phone }}</p>
                                </div>
                            </div>

                            {{-- activity --}}
                            <div class="row">
                                <div class="col">
                                    <p><strong>Activity:</strong> {{ $monitor->activity }}</p>
                                </div>
                            </div>

                            {{-- officer details --}}
                            <div class="row">
                                <p class="mt-2 font-weight-bold">Officer Details</p>
                                <div class="col">
                                    <p>Officer ID: {{ $monitor->staff_id }}</p>
                                </div>

                                <div class="col">
                                    <p>Officer Name: {{ $monitor->names }}</p>
                                </div>

                            </div>
                        </div>

                        {{-- show comments --}}
                        <div class="container border border-warning">
                            <div class="row">
                                <div class="col d-flex justify-content-between">
                                    <p class="font-weight-bold">Comments</p>
                                    <button class="btn btn-warning btn-sm text-white" title="Add Comment"
                                        data-bs-toggle="modal" data-bs-target="#addComment"
                                        data-sale-id="{{ $monitor->id }}">
                                        <i class="fa fa-plus" aria-hidden="true"></i> Comment
                                        </a>
                                </div>
                            </div>
                            @foreach ($monitor_comments as $monitor_comment)
                            <div class="row">
                                <div class="col d-flex justify-content-between">
                                    <p>{{$monitor_comment->id.' '.$monitor_comment->comment}}</p>
                                    <p>{{$monitor_comment->created_at}}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    <!-- Modal -->
    <!-- Comment Modal -->
    <div class="modal fade" id="addComment" tabindex="-1" role="dialog" aria-labelledby="addComment"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Add Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden input field to store the customer_id value -->
                    <input type="hidden" id="sale_id" name="sale_id">

                    <!-- Add your comment form fields here -->
                    <!-- For example, a textarea for entering the comment -->
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="10"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- Add a button to submit the form -->
                    <button type="button" class="btn btn-primary" id="submitComment">Submit</button>
                </div>
            </div>
        </div>
    </div>

    @push('dashboard')
        {{-- add sale id to the comment box on click --}}
        <script>
            //check if ready
            $(document).ready(function() {
                $('#addComment').on('show.bs.modal', function(event) {
                    console
                    var button = $(event.relatedTarget);
                    var sale_id = button.data('sale-id');
                    console.log("sales Id: ", sale_id);
                    var modal = $(this);
                    modal.find('.modal-body #sale_id').val(sale_id);
                });

                // submit comment
                $('#submitComment').click(function() {
                    var sale_id = $('#sale_id').val();
                    var comment = $('#comment').val();

                    // send the data to the server
                    $.ajax({
                        url: '/add-activity-comment',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            sales_activity_id: sale_id,
                            comment: comment
                        },
                        success: function(response) {
                            console.log(response);
                            // close the modal
                            $('#addComment').modal('hide');
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                });
            });
        </script>
    @endpush
