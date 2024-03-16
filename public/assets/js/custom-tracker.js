$(document).ready(function () {
    // Declare the table variable outside the function scope
    var table;

    // Initialize DataTable
    table = drawTable($('#group-by').val());
    // Populate initial table headers
    var initialGroup = $('#group-by').val();
    populateTableHeaders(initialGroup);
    fetchData(initialGroup);
    // Handle change event for the select input
    $('#group-by').change(function () {
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
            "branches": ["Region", "Branch", "Actual Volume", "Actual Clients", "Target Volume", "Target Clients", "Variance", "%score"],
            "products": ["Branch", "Product", "Actual Volume", "Actual Clients", "Target Volume", "Target Clients", "Variance", "%score"],
            "officers": ["Officer ID", "Names", "Volume Disbursed", "Number Of Clients"],
        };

        // Get the corresponding headers for the selected group
        var selectedHeaders = headers[group];

        // Clear existing columns and add the new ones
        table.columns().header().to$().empty(); // Clear all column headers
        $.each(selectedHeaders, function (index, header) {
            table.columns(index).header().to$().text(header); // Set new column headers
        });
    }



    // Function to fetch data
    function fetchData(group) {
        // Fetch data based on the selected group
        $.ajax({
            url: "/sales-group-by",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                "group": group
            },
            success: function (response) {
                var data = response.data;
                console.log(data);
                table.columns().visible( [true, true, true, true, true, true, true, true]);
                table.clear().draw(); // Clear existing data before populating and redraw
                $.each(data, function (index, item) {
                    if (group === 'products') {
                        var row = [
                            item.branch_name,
                            item.product_name,
                            item.total_disbursement_amount.toLocaleString(),
                            item.actual_clients.toLocaleString(),
                            item.target_amount.toLocaleString(),
                            item.target_clients.toLocaleString(),
                            item.balance.toLocaleString(),
                            item.score.toLocaleString()
                        ];
                    } else if (group === 'branches') {
                        var row = [
                            item.region_name,
                            item.branch_name,
                            item.total_disbursement_amount.toLocaleString(),
                            item.actual_clients.toLocaleString(),
                            item.target_amount.toLocaleString(),
                            item.target_clients.toLocaleString(),
                            item.balance.toLocaleString(),
                            item.score.toLocaleString()
                        ];
                    } else {
                        var row = [
                            item.staff_id,
                            item.names,
                            item.total_disbursement_amount.toLocaleString(),
                            item.number_of_clients.toLocaleString(),
                            '',
                            '',
                            '',
                            ''
                        ];
                        table.columns([4, 5, 6, 7]).visible(false);

                    }
                    table.row.add(row).draw();
                });

                // Stop the spinner and display content
                // $("#content").show();
                // $("#spinner").hide();
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                console.log(status);
                console.log(error);
            }
        });
    }

    //generate messageTop property for the export buttons based on the selected group
    function generateMessageTop(group) {
        var messageTop = "Exported from the Arrears Report";
        switch (group) {
            case "branches":
                messageTop = "Performance Report by Branch";
                break;
            case "products":
                messageTop = "Performance Report by Product";
                break;
        }
        return messageTop;
    }

    function drawTable(groupedby) {
        // Define column titles based on the selected group
        var columns;
        switch (groupedby) {
            case 'branches':
            case 'products':
                columns = [
                    { title: "" },
                    { title: "" },
                    { title: "" },
                    { title: "" },
                    { title: "" },
                    { title: "" },
                    { title: "" },
                    { title: "" }
                ];
                break;
            case 'officers':
                columns = [
                    { title: "" },
                    { title: "" },
                    { title: "" },
                    { title: "" }
                ];
                break;
            default:
                columns = [];
        }

        // Initialize DataTable
        var table = $('#branch-performance').DataTable({
            //define
            responsive: true,
            columns: columns,
            //show sortable headers with arrows
            "footerCallback": function (row, data, start, end, display) {
                var api = this.api();
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
                if (groupedby === 'branches' || groupedby === 'products') {
                    $(api.column(2).footer()).html(sum(api.column(2).data())); // Actual Volume
                    $(api.column(3).footer()).html(sum(api.column(3).data())); // Actual Clients
                    $(api.column(4).footer()).html(sum(api.column(4).data())); // Target Volume
                    $(api.column(5).footer()).html(sum(api.column(5).data())); // Target Clients
                    $(api.column(6).footer()).html(sum(api.column(6).data())); // Balance
                    $(api.column(7).footer()).html(sum(api.column(7).data())); // %Score
                } else if (groupedby === 'officers') {
                    $(api.column(2).footer()).html(sum(api.column(2).data())); // Volume Disbursed
                    $(api.column(3).footer()).html(sum(api.column(3).data())); // Number of Clients
                }

            },
        });

        // Set initial visibility for columns
        var initialColumnsVisibility = [true, true, true, true, true, true, true, true];
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
                            messageTop: generateMessageTop($('#group-by').val())
                        },
                        {
                            extend: 'excel',
                            className: 'export-button',
                            messageTop: generateMessageTop($('#group-by').val())
                        },
                        {
                            extend: 'pdf',
                            className: 'export-button',
                            messageTop: generateMessageTop($('#group-by').val())
                        },
                        {
                            extend: 'print',
                            className: 'export-button',
                            messageTop: generateMessageTop($('#group-by').val())
                        }
                    ], // List of buttons in the dropdown
                    className: 'btn btn-warning dropdown-toggle btn-lg' // Bootstrap button classes
                }
            ]

        });


        // Add the export buttons to the container
        table.buttons().container().appendTo($('#export-buttons'));

        return table;
    }
});
