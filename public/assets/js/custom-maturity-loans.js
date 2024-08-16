$(document).ready(function () {
    var table;
    table = drawTable();

    var initialGroup = 'staff_id';
    populateTableHeaders(initialGroup);
    fetchData(initialGroup);

    $('#staff').change(function () {
        $("#content").hide();
        $("#spinner").show();
        var selectedGroup = $(this).val();
        populateTableHeaders(selectedGroup);
        fetchData(selectedGroup);
    });

    function populateTableHeaders(group) {
        var headers = {
            "staff_id": ["Branch", "Client ID", "Names", "Phone Number", "Loan Product Name", "Loan Amount", "Maturity Date"],
        };

        var selectedHeaders = headers[group];

        table.columns().header().to$().empty();
        $.each(selectedHeaders, function (index, header) {
            table.columns(index).header().to$().text(header);
        });
    }

    function fetchData(group) {
        $.ajax({
            url: "/maturity-loans-group-by",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { "group": group },
            success: function (response) {
                var data = response.data;
                var rows = [];
                var branchNames = [];
                var productNames = [];

                $.each(data, function (index, item) {
                    var row = [
                        item.branch_name,
                        item.names == "" ? 'N/A' : item.customer_id,
                        item.names == "" ? 'N/A' : item.names,
                        item.phone == "" ? 'N/A' : item.phone,
                        item.product_name,
                        item.amount_disbursed.toLocaleString(),
                        item.maturity_date,
                    ];

                    rows.push(row);

                    // Collect unique branch names
                    if (item.branch_name && branchNames.indexOf(item.branch_name) === -1) {
                        branchNames.push(item.branch_name);
                    }

                    // Collect unique product names
                    if (item.product_name && productNames.indexOf(item.product_name) === -1) {
                        productNames.push(item.product_name);
                    }
                });

                // Populate branch and product dropdowns
                populateDropdown('#branchFilter', branchNames);
                populateDropdown('#productFilter', productNames);

                // Clear existing data before populating
                table.clear();

                // Add all rows at once
                table.rows.add(rows).draw();
                applyFilters();

                $("#content").show();
                $("#spinner").hide();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function populateDropdown(selector, items) {
        var dropdown = $(selector);
        dropdown.empty(); // Clear existing options
        // decide to say all branches or all products
        if (selector == '#branchFilter') {
            dropdown.append('<option value="">All Branches</option>'); // Default option
        } else {
            dropdown.append('<option value="">All Products</option>'); // Default option
        }

        $.each(items, function (index, item) {
            dropdown.append('<option value="' + item + '">' + item + '</option>');
        });
    }



    function applyFilters() {
        $('#branchFilter').on('change', function () {
            var branchValue = this.value;
            table.column(0).search(branchValue).draw();
        });

        $('#productFilter').on('change', function () {
            var productValue = this.value;
            table.column(4).search(productValue).draw();
        });

        $('#clientIdFilter').on('keyup', function () {
            table.column(1).search(this.value).draw();
        });

        $('#phoneFilter').on('keyup', function () {
            table.column(3).search(this.value).draw();
        });

        $('#maturityDateFilter').on('keyup', function () {
            table.column(6).search(this.value).draw();
        });
    }


    function drawTable() {
        var table = $('#maturity-loans').DataTable({
            responsive: false,
            columns: [
                { title: "Branch" },
                { title: "Client ID" },
                { title: "Names" },
                { title: "Phone Number" },
                { title: "Loan Product Name" },
                { title: "Loan Amount Closing" },
                { title: "Maturity Date" }
            ],
        });

        new $.fn.dataTable.Buttons(table, {
            buttons: [
                {
                    extend: 'collection',
                    text: 'Export',
                    buttons: [
                        {
                            extend: 'csv',
                            className: 'export-button',
                        },
                        {
                            extend: 'excel',
                            className: 'export-button',
                        },
                        {
                            extend: 'pdf',
                            className: 'export-button',
                            customize: function (doc) {
                                doc.styles.tableHeader.fillColor = '#FFA500';
                            }
                        },
                        {
                            extend: 'print',
                            className: 'export-button',
                        }
                    ],
                    className: 'btn btn-warning dropdown-toggle btn-lg text-white'
                }
            ]
        });

        table.buttons().container().appendTo($('#export-buttons'));

        return table;
    }

    // Initialize date picker for maturity date filter
    $('#maturityDateFilter').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true,
    });
});
