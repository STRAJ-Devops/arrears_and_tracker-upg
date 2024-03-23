$(document).ready(function () {
    // Initialize DataTable
    var table = $('#Incentives').DataTable(
        {
            dom: "Bfrtip",
            //style the buttons
            buttons: [
                {
                    extend: "csv",
                    className: "btn btn-warning btn-small",
                    messageTop: "Officer Incentives",
                },
                {
                    extend: "excel",
                    className: "btn btn-warning btn-small",
                    messageTop: "Officer Incentives",
                },
                {
                    extend: "pdf",
                    className: "btn btn-warning btn-small",
                    messageTop: "Officer Incentives",
                },
                {
                    extend: "print",
                    className: "btn btn-warning btn-small",
                    messageTop: "Officer Incentives",
                },
            ],
        }
    );

    // Function to fetch data
    function fetchData() {

        $.ajax({
            url: "/get-incentives",
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
                    //export buttons

            success: function (response) {
                var tbody = $('#Incentives tbody');
                tbody.empty(); // Clear existing data

                $.each(response.incentives, function (index, item) {
                    console.log(item);
                    var officerDetails = item.officer_details;
                    var incentivesDetails = item.incentive;
                    var row = [
                        index,
                        officerDetails.names,
                        incentivesDetails.outstanding_principal_individual??0,
                        incentivesDetails.outstanding_principal_group??0,
                        incentivesDetails.unique_customer_id_individual??0,
                        incentivesDetails.records_for_unique_group_id_group??0,
                        incentivesDetails.records_for_PAR??0,
                        incentivesDetails.monthly_loan_loss_rate??0,
                        incentivesDetails.sgl_records??0,
                    ];
                    table.row.add(row).draw();
                });

                // Stop the spinner and display content
                $("#content").show();
                $("#spinner").hide();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    // Call the fetchData function
    fetchData();
});
