$(document).ready(function () {
    // Check logged_user variable and show corresponding section
    if (logged_user === 1) {
        $("#table-section").show(); // Show the table section if user is logged in
        // Initialize DataTable
        var table = $('#Incentives').DataTable({
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
                    orientation: "landscape",
                },
                {
                    extend: "print",
                    className: "btn btn-warning btn-small",
                    messageTop: "Officer Incentives",
                    orientation: "landscape",
                },
            ],
        });

    }

    // Function to fetch data
    function fetchData() {
        console.log("Fetching data...");
        $.ajax({
            url: "/get-incentives",
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            //export buttons

            success: function (response) {
                if (typeof logged_user !== 'undefined' && logged_user === 1) {
                    $("#general-section").empty();
                    var tbody = $('#Incentives tbody');
                    tbody.empty(); // Clear existing data

                    $.each(response.incentives, function (index, item) {
                        console.log(item);
                        var officerDetails = item.officer_details;
                        var incentivesDetails = item.incentive;
                        var row = [
                            index,
                            officerDetails.names,
                            Number(incentivesDetails.outstanding_principal_individual).toLocaleString() ?? 0,
                            Number(incentivesDetails.outstanding_principal_group).toLocaleString() ?? 0,
                            Number(incentivesDetails.unique_customer_id_individual).toLocaleString() ?? 0,
                            Number(incentivesDetails.records_for_unique_group_id_group).toLocaleString() ?? 0,
                            incentivesDetails.records_for_PAR ?? 0,
                            incentivesDetails.monthly_loan_loss_rate ?? 0,
                            Number(incentivesDetails.sgl_records).toLocaleString() ?? 0,
                            Number(incentivesDetails.incentive_amount_PAR).toLocaleString() ?? 0,
                            Number(incentivesDetails.incentive_amount_Net_Portifolio_Growth).toLocaleString() ?? 0,
                            Number(incentivesDetails.incentive_amount_Net_Client_Growth).toLocaleString() ?? 0,
                            Number(incentivesDetails.total_incentive_amount).toLocaleString() ?? 0,

                        ];
                        table.row.add(row).draw();
                    });

                    // Stop the spinner and display content
                    $("#general-section").show();
                    $("#spinner").hide();
                } else {
                    // Clear existing content
                    $("#general-section").empty();
                    if(response.incentives.length === 0){
                        $("#general-section").append(`
                            <div class="card bg-orange rounded-lg shadow">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-uppercase font-weight-bold">No incentives available</h5>
                                </div>
                            </div>
                        `);
                    } else{
                        $("#incentives-card-section").show();

                    // Create cards for each officer
                    $.each(response.incentives, function (index, item) {
                        var officerDetails = item.officer_details;
                        var incentivesDetails = item.incentive;
                        var cardHTML = createCard(officerDetails, incentivesDetails);
                        $("#general-section").append(cardHTML);
                    });
                }

                    // Stop the spinner and display content
                    $("#spinner").hide();
                    $("#general-section").show();
                }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

    function createCard(officerDetails, incentivesDetails) {
        return `
            <div class="card bg-orange rounded-lg shadow">
                <div class="row no-gutters">
                    <div class="col-md-4">
                        <img src="assets/img/reward.png" alt="Incentives Illustration" class="img-fluid mt-3 ml-3">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body text-left">
                            <h5 class="card-title text-uppercase font-weight-bold">${officerDetails.names}</h5>
                            <hr>
                            <p class="card-text"><strong>Outstanding principal (Individual):</strong> ${(parseFloat(incentivesDetails.outstanding_principal_individual)).toLocaleString() ?? 0}/=</p>
                            <p class="card-text"><strong>Outstanding principal (Group):</strong> ${(parseFloat(incentivesDetails.outstanding_principal_group)).toLocaleString() ?? 0}/=</p>
                            <p class="card-text"><strong>Number of Customers(Individual):</strong> ${Number(incentivesDetails.unique_customer_id_individual).toLocaleString() ?? 0}</p>
                            <p class="card-text"><strong>Number of Customers(Group):</strong> ${Number(incentivesDetails.records_for_unique_group_id_group).toLocaleString() ?? 0}</p>
                            <p class="card-text"><strong>PAR>1Day:</strong> ${incentivesDetails.records_for_PAR ?? 0}%</p>
                            <p class="card-text"><strong>Monthly Loan Loss Rate:</strong> ${incentivesDetails.monthly_loan_loss_rate ?? 0}%</p>
                            <p class="card-text"><strong>Number Of Groups:</strong> ${(incentivesDetails.sgl_records).toLocaleString() ?? 0}</p>
                            <p class="card-text"><strong>Incentive amount (PAR):</strong> ${(parseFloat(incentivesDetails.incentive_amount_PAR)).toLocaleString() ?? 0}/=</p>
                            <p class="card-text"><strong>Incentive amount (Net Portfolio Growth):</strong> ${(parseFloat(incentivesDetails.incentive_amount_Net_Portifolio_Growth)).toLocaleString() ?? 0}/=</p>
                            <p class="card-text"><strong>Incentive amount (Net Client Growth):</strong> ${(parseFloat(incentivesDetails.incentive_amount_Net_Client_Growth)).toLocaleString() ?? 0}/=</p>
                            <p class="card-text h5"><strong>Total incentive amount:</strong> ${(parseFloat(incentivesDetails.total_incentive_amount)).toLocaleString() ?? 0}/=</p>
                        </div>
                    </div>
                </div>
            </div>`;
    }



    // Call the fetchData function
    fetchData();
});
