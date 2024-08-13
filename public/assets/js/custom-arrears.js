$(document).ready(function () {
    var table;
    table = drawTable();
    // Populate initial table headers
    var initialGroup = $('#staff').val();
    populateTableHeaders(initialGroup);
    fetchData(initialGroup);
    // Handle change event for the select input
    $('#staff').change(function () {
        // Hide content and display spinner
        $("#content").hide();
        $("#spinner").show();
        var selectedGroup = $(this).val();
        populateTableHeaders(selectedGroup);
        fetchData(selectedGroup);
    });


    // Function to populate table headers
    function populateTableHeaders(group) {

        var headers = {
            // define headers that belong to the institution
            "staff_id": ["Officer ID", "Names", "Clients", "Outstanding Principal", "Principle Arrears", "Interest Arrears", "Total Arrears", "Clients in Arrears", "Par>1 Day(%)"],
            "branch_id": ["Branch", "Name", "Clients", "Outstanding Principal", "Principle Arrears", "Interest Arrears", "Total Arrears", "Clients in Arrears", "Par>1 Day(%)"],
            "region_id": ["Region", "Name", "Clients", "Outstanding Principal", "Principle Arrears", "Interest Arrears", "Total Arrears", "Clients in Arrears", "Par>1 Day(%)"],
            "loan_product": ["Name", "Clients", "Outstanding Principal", "Principle Arrears", "Interest Arrears", "Total Arrears", "Clients in Arrears", "Par>1 Day(%)"],
            "gender": ["Name", "Clients", "Outstanding Principal", "Principle Arrears", "Interest Arrears", "Total Arrears", "Clients in Arrears", "Par>1 Day(%)"],
            "district": ["District", "Name", "Clients", "Outstanding Principal", "Principle Arrears", "Interest Arrears", "Total Arrears", "Clients in Arrears", "Par>1 Day(%)"],
            "sub_county": ["Sub County", "Name", "Clients", "Outstanding Principal", "Principle Arrears", "Interest Arrears", "Total Arrears", "Clients in Arrears", "Par>1 Day(%)"],
            "village": ["Village", "Name", "Clients", "Outstanding Principal", "Principle Arrears", "Interest Arrears", "Total Arrears", "Clients in Arrears", "Par>1 Day(%)"],
            "client": ["Customer ID", "Names", "comments", "Amount Disbursed", "Outstanding Principal", "Principle arrears", "Interest Arrears", "Total Arrears", "Number Of Days Late", "actions"],
            "age": ["Age Bracket", "Number of clients", "Principle Arrears", "Interest Arrears", "Total Arrears"],
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
            url: "/arrears-group-by",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "group": group
            },
            success: function (response) {
                var data = response.data;
                var rows = [];
                $.each(data, function (index, item) {
                    if (group === 'age') {
                        var row = [
                            item.group_key,
                            item.total_clients.toLocaleString(), // Pad with comma after every three digits
                            item.total_outstanding_principal.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_arrears.toLocaleString(), // Pad with comma after every three digits
                            '',
                            '',
                            '',
                            ''
                        ];
                    } else if (group === 'loan_product') {
                        var row = [
                            item.names,
                            item.total_clients.toLocaleString(), // Pad with comma after every three digits
                            item.total_outstanding_principal.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_arrears.toLocaleString(), // Pad with comma after every three digits
                            '',
                            '',
                            '',
                            ''
                        ];
                    } else if (group === 'gender') {
                        var row = [
                            item.group_key,
                            item.total_clients.toLocaleString(), // Pad with comma after every three digits
                            item.total_outstanding_principal.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_par.toLocaleString(),
                            '',
                            '',
                            ''
                        ];
                    } else if (group === 'client') {

                        var numberOfCommentsHtml = '<button class="btn btn-sm btn-outline-primary view-comments" data-customer-id="' + item.customer_id + '">' + item.number_of_comments.toLocaleString() + '</button>';
                        var row = [
                            item.group_key,
                            item.names,
                            numberOfCommentsHtml,
                            item.amount_disbursed.toLocaleString(), // Pad with comma after every three digits
                            item.total_outstanding_principal.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.number_of_days_late.toLocaleString(),
                            '<button class="btn btn-primary comment-button" data-customer-id="' + item.customer_id + '" data-nodl="' + item.number_of_days_late.toLocaleString() + '"><i class="fa fa-commenting" aria-hidden="true"></i></button>'
                        ];


                    } else {
                        var row = [
                            item.group_key,
                            item.names,
                            item.total_clients.toLocaleString(), // Pad with comma after every three digits
                            item.total_outstanding_principal.toLocaleString(), // Pad with comma after every three digits
                            item.total_principle_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_interest_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.clients_in_arrears.toLocaleString(), // Pad with comma after every three digits
                            item.total_par.toLocaleString(),
                            ''
                        ];
                    }
                    rows.push(row);
                });

                // Clear existing data before populating
                table.clear();

                // Add all rows at once
                table.rows.add(rows).draw();

                if (group === 'age' || group === 'loan_product') {
                    table.columns([6, 7, 8, 9]).visible(false);
                } else if (group === 'gender') {
                    table.columns([7, 8, 9]).visible(false);
                } else if (group === 'staff_id') {
                    table.columns([9]).visible(false);
                } else if (group === 'client') {
                    if (logged_user == 5) {
                        table.columns([9]).visible(false);
                        table.columns([2]).visible(false);
                    } else {
                        //make all columns visible
                        table.columns().visible(true);
                    }
                }
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
                messageTop = "Exported from the Arrears Report by Staff";
                break;
            case "branch_id":
                messageTop = "Exported from the Arrears Report by Branch";
                break;
            case "region_id":
                messageTop = "Exported from the Arrears Report by Region";
                break;
            case "loan_product":
                messageTop = "Exported from the Arrears Report by Loan Product";
                break;
            case "gender":
                messageTop = "Exported from the Arrears Report by Gender";
                break;
            case "district":
                messageTop = "Exported from the Arrears Report by District";
                break;
            case "sub_county":
                messageTop = "Exported from the Arrears Report by Sub County";
                break;
            case "village":
                messageTop = "Exported from the Arrears Report by Village";
                break;
        }
        return messageTop;
    }

    function drawTable() {
        // Initialize DataTable
        var table = $('#arrears').DataTable({
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



                // Calculate total for each column
                //check if group is client
                $(api.column(2).footer()).html(sum(api.column(2).data())); // Principle Arrears
                $(api.column(3).footer()).html(sum(api.column(3).data())); // Interest Arrears
                $(api.column(4).footer()).html(sum(api.column(4).data())); // Total Arrears
                $(api.column(5).footer()).html(sum(api.column(5).data())); // Clients in Arrears
                $(api.column(6).footer()).html(sum(api.column(6).data())); // Par
                $(api.column(7).footer()).html(sum(api.column(7).data())); // Total
                $(api.column(8).footer()).html(sum(api.column(8).data())); // Total
            },
        });

        // Set initial visibility for columns
        var initialColumnsVisibility = [true, true, true, true, true, true, true, true, true, true];
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
                            messageTop: generateMessageTop($('#staff').val()),
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
                            customize: function (doc) {
                                doc.styles.tableHeader.fillColor = '#FFA500';
                            }
                        },
                        {
                            extend: 'print',
                            className: 'export-button',
                            messageTop: generateMessageTop($('#staff').val()),
                        }
                    ], // List of buttons in the dropdown
                    className: 'btn btn-warning dropdown-toggle btn-lg text-white' // Bootstrap button classes
                }
            ]

        });


        // Add the export buttons to the container
        table.buttons().container().appendTo($('#export-buttons'));

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
