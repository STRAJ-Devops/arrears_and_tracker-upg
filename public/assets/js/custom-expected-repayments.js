$(document).ready(function () {
    // Initialize datepicker
    $('#date-filter').datepicker({
        dateFormat: 'dd-M-y',
        onSelect: function (dateText, inst) {
            table.column(11).search(dateText).draw(); // Assuming next repayment date is in column index 10
        }
    });
    //set the initial date to today
    $('#date-filter').datepicker('setDate', new Date());

    //show the calendar
    $('#date-filter').datepicker('show');


    var table;
    table = drawTable();
    // Populate initial table headers
    var initialGroup = $('#staff').val() ? $('#staff').val() : 'client';
    populateTableHeaders(initialGroup);
    fetchData(initialGroup);

    if ($('#staff').val() === undefined) {
        var today = new Date();
        // filterByDate(today);
        $('#today').addClass('active'); // Highlight the "Today" button

    }
    // Handle change event for the select input
    $('#staff').change(function () {
        // Hide content and display spinner
        $("#content").hide();
        $("#spinner").show();
        var selectedGroup = $(this).val();
        populateTableHeaders(selectedGroup);
        fetchData(selectedGroup);
    });


    // Function to filter the DataTable by date
    function filterByDate(date) {
        //convert the date to the format "10-Mar-24"
        var formattedDate = $.datepicker.formatDate("dd-M-y", date);
        //search for rows with formattedDate or '' column 11
        table.column(11).search(formattedDate).draw();
    }




    // Function to populate table headers
    function populateTableHeaders(group) {
        var headers = {
            // define headers that belong to the institution
            "staff_id": ["Branch ID", "Officer ID", "Names", "Clients", "Next Repayment Principal", "Principal In Arrears", "Total Expected Principal", "Next Repayment Interest", "Interest In Arrears", "Total Expected Interest", "Expected Total", "Clients in Arrears"],
            "branch_id": ["Branch", "Name", "Clients", "Next Repayment Principal", "Principal In Arrears", "Total Expected Principal", "Next Repayment Interest", "Interest In Arrears", "Total Expected Interest", "Expected Total", "Clients in Arrears"],
            "region_id": ["Region", "Name", "Clients", "Next Repayment Principal", "Principal In Arrears", "Total Expected Principal", "Next Repayment Interest", "Interest In Arrears", "Total Expected Interest", "Expected Total", "Clients in Arrears"],
            "loan_product": ["Name", "Clients", "Next Repayment Principal", "Principal In Arrears", "Total Expected Principal", "Next Repayment Interest", "Interest In Arrears", "Total Expected Interest", "Expected Total", "Clients in Arrears"],
            "gender": ["Name", "Clients", "Next Repayment Principal", "Principal In Arrears", "Total Expected Principal", "Next Repayment Interest", "Interest In Arrears", "Total Expected Interest", "Expected Total", "Clients in Arrears"],
            "district": ["District", "Name", "Clients", "Next Repayment Principal", "Principal In Arrears", "Total Expected Principal", "Next Repayment Interest", "Interest In Arrears", "Total Expected Interest", "Expected Total", "Clients in Arrears"],
            "sub_county": ["Sub County", "Name", "Clients", "Next Repayment Principal", "Principal In Arrears", "Total Expected Principal", "Next Repayment Interest", "Interest In Arrears", "Total Expected Interest", "Expected Total", "Clients in Arrears"],
            "village": ["Village", "Name", "Clients", "Next Repayment Principal", "Principal In Arrears", "Total Expected Principal", "Next Repayment Interest", "Interest In Arrears", "Total Expected Interest", "Expected Total", "Clients in Arrears"],
            "client": ["Branch ID", "Customer ID", "Customer Name", "Phone Number", "Disbursement Amount", "Outstanding Principal", "DDA", "Next Repayment Principal", "Next Repayment Interest", "Principal In Arrears", "Interest In Arrears", "Total Expected Amount", "Maturity Date"],
            "age": ["Age Bracket", "Number of clients", "Next Repayment Principal", "Principal In Arrears", "Total Expected Principal", "Next Repayment Interest", "Interest In Arrears", "Total Expected Interest", "Expected Total"],
        };

        // Get the corresponding headers for the selected group
        var selectedHeaders = headers[group];

        //clean any existing columns
        table.columns().header().to$().empty();
        $.each(selectedHeaders, function (index, header) {
            table.columns(index).header().to$().text(header);
        });
    }

    // Function to fetch data
    function fetchData(group) {
        // Fetch data based on the selected group
        $.ajax({
            url: "/get-expected-repayments",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "group": group
            },
            success: function (response) {
                var data = response.data;
                var tbody = $('#arrears tbody');
                var rows = [];
                table.columns().visible([true, true, true, true, true, true, true, true, true, true, true, true, true, true])
                table.clear().draw(); // Clear existing data before populating and redraw

                $.each(data, function (index, item) {
                    if (group === 'age') {
                        var row = [
                            item.group_key,
                            item.total_clients.toLocaleString(), // Pad with comma after every three digits
                            item.next_repayment_principal.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.expected_principal.toLocaleString(), // Pad with comma after every three digits
                            item.next_repayment_interest.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.expected_interest.toLocaleString(), // Pad with comma after every three digits
                            item.expected_total.toLocaleString(), // Pad with comma after every three digits
                            '',
                            '',
                            '',
                            ''
                        ];

                        //hide the last 3 columns
                        table.columns([9, 10, 11, 12]).visible(false);
                    } else if (group === 'loan_product') {
                        var row = [
                            item.names,
                            item.total_clients.toLocaleString(), // Pad with comma after every three digits
                            item.next_repayment_principal.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.expected_principal.toLocaleString(), // Pad with comma after every three digits
                            item.next_repayment_interest.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.expected_interest.toLocaleString(), // Pad with comma after every three digits
                            item.expected_total.toLocaleString(), // Pad with comma after every three digits
                            '',
                            '',
                            '',
                            ''
                        ];

                        //hide the last 3 columns
                        table.columns([9, 10, 11, 12]).visible(false);
                    } else if (group === 'gender') {
                        var row = [
                            item.group_key,
                            item.next_repayment_principal.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.expected_principal.toLocaleString(), // Pad with comma after every three digits
                            item.next_repayment_interest.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.expected_interest.toLocaleString(), // Pad with comma after every three digits
                            item.expected_total.toLocaleString(), // Pad with comma after every three digits
                            '',
                            '',
                            '',
                            '',
                            ''
                        ];

                        //hide the last 3 columns
                        table.columns([9, 10, 11, 12]).visible(false);
                    } else if (group === 'client') {
                        var row = [
                            item.branch_id,
                            item.customer_id,
                            item.names,
                            item.phone_number,

                            item.amount_disbursed.toLocaleString(), // Pad with comma after every three digits
                            item.total_outstanding_principal.toLocaleString(), // Pad with comma after every three digits
                            item.add_per_customer.toLocaleString(), // Pad with comma after every three digits
                            item.next_repayment_principal.toLocaleString(), // Pad with comma after every three digits

                            item.next_repayment_interest.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits

                            item.total_payment_amount.toLocaleString(), // Pad with comma after every three digits
                            item.maturity_date??'N/A'
                        ];
                    } else {
                        var row = [
                            item.branch_id,
                            item.group_key,
                            item.names,
                            item.total_clients.toLocaleString(), // Pad with comma after every three digits
                            item.next_repayment_principal.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.expected_principal.toLocaleString(), // Pad with comma after every three digits
                            item.next_repayment_interest.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.expected_interest.toLocaleString(), // Pad with comma after every three digits
                            item.expected_total.toLocaleString(), // Pad with comma after every three digits
                            item.clients_in_arrears.toLocaleString(), // Pad with comma after every three digits
                            ''
                        ];

                        //hide the last 3 columns
                        table.columns([11,12]).visible(false);
                    }
                    rows.push(row);
                });

                // Clear existing data before populating
                table.clear();

                // Add all rows at once
                table.rows.add(rows).draw();

                // Stop the spinner and display content
                $("#content").show();
                $("#spinner").hide();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    //generate messageTop property for the export buttons based on the selected group
    function generateMessageTop(group) {
        var messageTop = "Exported from the Arrears Report";
        switch (group) {
            case "staff_id":
                messageTop = "Exported from the Expected Payments Report by Staff";
                break;
            case "branch_id":
                messageTop = "Exported from the Expected Payments Report by Branch";
                break;
            case "region_id":
                messageTop = "Exported from the Expected Payments Report by Region";
                break;
            case "loan_product":
                messageTop = "Exported from the Expected Payments Report by Loan Product";
                break;
            case "gender":
                messageTop = "Exported from the Expected Payments Report by Gender";
                break;
            case "district":
                messageTop = "Exported from the Expected Payments Report by District";
                break;
            case "sub_county":
                messageTop = "Exported from the Expected Payments Report by Sub County";
                break;
            case "village":
                messageTop = "Exported from the Expected Payments Report by Village";
                break;
        }
        return messageTop;
    }

    function drawTable() {
        // Initialize DataTable
        var table = $('#expected-repayments-table').DataTable({
            responsive: false,
            columns: [
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" },
                { title: "" }
            ],
            //show sortable headers with arrows
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();

                // Total function
                // Total function
                // Total function
                var sum = function (data) {
                    var total = data.reduce(function (acc, value) {
                        // Remove commas and parse as float
                        var floatValue = parseFloat(value.toString().replace(/,/g, ''));
                        // Add to accumulator
                        return acc + (isNaN(floatValue) ? 0 : floatValue);
                    }, 0);
                    return total.toLocaleString(); // Format total with commas
                };

                if (($('#staff').val() ? $('#staff').val() : 'client') === 'client') {
                    $(api.column(2).footer()).html(""); // Expected PRincipal
                    $(api.column(3).footer()).html(""); // Expected PRincipal
                    $(api.column(4).footer()).html(sum(api.column(4).data())); // Expected Total
                    $(api.column(5).footer()).html(sum(api.column(5).data())); // Clients in Arrears
                    $(api.column(6).footer()).html(sum(api.column(6).data())); // Par
                    $(api.column(7).footer()).html(sum(api.column(7).data())); // Total
                    $(api.column(8).footer()).html(sum(api.column(8).data())); // Total
                    $(api.column(9).footer()).html(sum(api.column(9).data())); // Total
                    $(api.column(10).footer()).html(sum(api.column(10).data())); // Total
                    $(api.column(11).footer()).html(sum(api.column(11).data())); // Total
                } else {
                    // Calculate total for each column
                    //check if group is client
                    // $(api.column(2).footer()).html(sum(api.column(2).data())); // Expected PRincipal
                    $(api.column(3).footer()).html(sum(api.column(3).data())); // Expected Interest
                    $(api.column(4).footer()).html(sum(api.column(4).data())); // Expected Total
                    $(api.column(5).footer()).html(sum(api.column(5).data())); // Clients in Arrears
                    $(api.column(6).footer()).html(sum(api.column(6).data())); // Par
                    $(api.column(7).footer()).html(sum(api.column(7).data())); // Total
                }
            },
        });

        // Set initial visibility for columns
        var initialColumnsVisibility = [true, true, true, true, true, true, true, true, true, true, true, true, true, true];
        table.columns().visible(initialColumnsVisibility);

        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'collection', // Use a collection button
                    text: 'Export', // Button text

                    buttons: [
                        {
                            extend: 'csv',
                            className: 'export-button',
                            messageTop: generateMessageTop($('#staff').val())
                        },
                        {
                            extend: 'excel',
                            className: 'export-button',
                            messageTop: generateMessageTop($('#staff').val())
                        },
                        {
                            extend: 'pdf',
                            className: 'export-button',
                            messageTop: generateMessageTop($('#staff').val()),
                            orientation: 'landscape',
                            customize: function (doc) {
                                doc.styles.tableHeader.fillColor = '#FFA500';
                            }
                        },
                        {
                            extend: 'print',
                            className: 'export-button',
                            messageTop: generateMessageTop($('#staff').val()),
                            orientation: 'landscape',
                        }
                    ], // List of buttons in the dropdown
                    className: 'btn btn-warning dropdown-toggle btn-lg' // Bootstrap button classes
                }
            ]

        });


        // Add the export buttons to the container
        table.buttons().container().appendTo($('#export-buttons'));

        // Attach click event handler to comment buttons
        $('#arrears tbody').on('click', '.comment-button', function () {
            // Retrieve the arrear id from the data attribute of the clicked button
            var customerId = $(this).data('customer-id');
            // Set the arrear id value in the hidden input field
            $('#customer_id').val(customerId);
            // Show the modal with the comment box or perform any other action
            $('#commentModal').modal('show');

        });

        // Function to fetch comments for a specific custome_id
        function fetchComments(customerId) {
            // Make a GET request to fetch comments
            $.ajax({
                url: "/comments", // Replace with your server endpoint
                type: "GET",
                data: {
                    customer_id: customerId
                },
                success: function (response) {
                    var comments = response.comments;
                    var commentsContainer = $('#comments-container');
                    commentsContainer.empty(); // Clear existing comments

                    // Loop through comments and append them to the container with styling
                    comments.forEach(function (comment) {
                        var commentBox = $('<div class="comment-box mb-3 p-3 rounded shadow-sm"></div>');

                        // Add comment content with styling
                        var commentContent = $('<p class="comment-text mb-0"></p>')
                            .text(comment.comment)
                            .css({
                                fontSize: '16px', // Adjust font size as desired
                                lineHeight: '1.5', // Set line spacing for readability
                                fontFamily: 'sans-serif', // Use a user-friendly font
                                color: '#333' // Set text color for clarity
                            });

                        // Optional: Add user information (if available)
                        if (comment.created_at) {
                            var userSpan = $('<span class="user-info mr-2"></span>')
                                .text(formatDate(comment.created_at) + ': ');
                            commentBox.prepend(userSpan);
                        }

                        commentBox.append(commentContent);
                        commentsContainer.append(commentBox);
                    });
                },
                error: function (xhr, status, error) {
                    // Handle error
                    console.error("Error fetching comments:", xhr.responseText);
                }
            });
        }


        // Attach click event handler to view comments
        $('#arrears tbody').on('click', '.view-comments', function () {
            // Retrieve the arrear id from the data attribute of the clicked button
            var customerId = $(this).data('customer-id');
            // Call the fetchComments function with the retrieved customerId
            fetchComments(customerId);
            // Show the modal with the comment box or perform any other action
            $('#viewCommentsModal').modal('show');
        });



        // Add an event listener to the submit button in the modal
        $('#submitComment').click(function () {
            // Retrieve the custome_id value from the hidden input field
            var customerId = $('#customer_id').val();
            // Retrieve other form field values if needed
            var comment = $('#comment').val();

            // Make an AJAX request to submit the form data
            $.ajax({
                url: '/add-comment', // Replace with your server endpoint
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    customer_id: customerId,
                    comment: comment
                },
                success: function (response) {
                    //show a swal alert
                    Swal.fire({
                        title: 'Success',
                        text: 'Comment added successfully',
                        icon: 'success',
                        confirmButtonText: 'Ok',
                    });
                    $('#commentModal').modal('hide');
                    // Populate initial table headers
                    var initialGroup = $('#staff').val();
                    populateTableHeaders(initialGroup);
                    fetchData(initialGroup);

                },
                error: function (xhr, status, error) {
                    //show a swal alert
                    Swal.fire({
                        title: 'Error',
                        text: 'Error submitting comment',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    // Handle any errors that occur during the AJAX request
                    console.error('Error submitting comment:', xhr.responseText);
                    // Optionally, display an error message to the user
                }
            });
        });


        return table;
    }

    function formatDate(dateString) {
        const dateObj = new Date(dateString);
        const options = {
            day: "numeric",
            month: "long",
            year: "numeric",
        };
        return new Intl.DateTimeFormat("en", options).format(dateObj);
    }
});