@extends('layouts.user_type.auth')

@section('content')
    <div id="spinner" class="spinner-overlay">
        <div class="spinner"></div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div id="export-buttons-branch-performance"></div> <!-- Container for export buttons -->
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <div class="row card-header pb-0 d-flex justify-content-between">
                            <div class="col-md-9">
                                <div class="form-group d-flex">
                                    <label for="staff" class="mr-2">Group By</label>
                                    <select class="form-control staff shadow-none" id="staff">
                                        @if (Auth::user()->user_type == 5 || 4)
                                            <option value="staff_id">Officer</option>
                                            <option value="branch_id">Branch</option>
                                            <option value="region_id">Region</option>
                                            <option value="loan_product">Loan Product</option>
                                            <option value="gender">Gender</option>
                                            <option value="district">District</option>
                                            <option value="sub_county">Sub County</option>
                                            <option value="age">Age</option>
                                            <option value="village">Village</option>
                                            <option value="client">Client</option>
                                        @elseif (Auth::user()->user_type == 1)
                                            <option value="staff_id">Officer</option>
                                            <option value="branch_id">Branch</option>
                                            <option value="client">Client</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3 text-center"> <!-- Added text-center class -->
                                <div class="form-group">
                                    <div class="d-flex justify-content-center">
                                        <!-- Changed class to justify-content-center -->
                                        <div id="export-buttons"></div> <!-- Container for export buttons -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table id="arrears" class="display responsive" style="width:100%">
                            <tbody>
                            <tfoot align="right">
                                <tr>
                                    <th colspan="1">Over all total</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <!-- Comment Modal -->
    <div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Add Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Hidden input field to store the customer_id value -->
                    <input type="hidden" id="customer_id" name="customer_id">

                    <!-- Hidden input field to store the customer_id value -->
                    <input type="hidden" id="nodl" name="number_of_days_late">

                    <!-- Add your comment form fields here -->
                    <!-- For example, a textarea for entering the comment -->
                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
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

    <!-- Modal -->
    <!-- Comment Modal -->
    <div class="modal fade" id="viewCommentsModal" tabindex="-1" role="dialog" aria-labelledby="commentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commentModalLabel">Comments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="comments-container">
                        <!-- Comments will be dynamically added here -->
                    </div>
                </div>
            </div>
        </div>
    </div>


    @push('dashboard')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script>
            var logged_user = {!! json_encode($logged_user) !!};
        </script>

        <script src="{{ asset('assets/js/custom-arrears.js') }}"></script>
    @endpush
@endsection
