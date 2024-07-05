$(document).ready(function () {
    var tabEl = document.querySelectorAll('button[data-bs-toggle="pill"]');
    for (i = 0; i < tabEl.length; i++) {
        tabEl[i].addEventListener("shown.bs.tab", function (event) {
            activated_pane = document.querySelector(
                event.target.getAttribute("data-bs-target")
            );
            const deactivated_pane = document.querySelector(
                event.relatedTarget.getAttribute("data-bs-target")
            );
            // if there is a data table, kill it and redraw
            var tableId = "Incentives-" + activated_pane.id;
            if ($.fn.DataTable.isDataTable("#" + tableId)) {
                $("#" + tableId).DataTable().destroy();
                //clear the table
                $("#" + tableId + " tbody").empty();
            }
            // Hide content and display spinner
            $("#content").hide();
            $("#table-section").hide();
            $("#spinner").show();
            fetchData(activated_pane.id);
        });
    }
    function drawTable(table_id = "Incentives-general") {
        // Check logged_user variable and show corresponding section
        if (logged_user === 5 || logged_user === 4 || logged_user === 3 || logged_user === 2) {
            $("#table-section").show(); // Show the table section if user is logged in
            // Initialize DataTable
            var table = $('#' + table_id).DataTable({
                dom: "Bfrtip",
                screenX: true,
                //style the buttons
                buttons: [
                    {
                        extend: "csv",
                        className: "btn btn-warning btn-small text-light",
                        messageTop: "Officer Incentives",
                    },
                    {
                        extend: "excel",
                        className: "btn btn-warning btn-small text-light",
                        messageTop: "Officer Incentives",
                    },
                    {
                        extend: "pdf",
                        className: "btn btn-warning btn-small text-light",
                        messageTop: "Officer Incentives",
                        orientation: "landscape",
                        customize: function (doc) {
                            doc.styles.tableHeader.fillColor = '#FFA500';
                        }
                    },
                    {
                        extend: "print",
                        className: "btn btn-warning btn-small text-light",
                        messageTop: "Officer Incentives",
                        orientation: "landscape",
                    },
                ],
            });

        }
        return table;
    }

    // Function to fetch data
    function fetchData(activated_pane = "general") {
        $.ajax({
            url: "/get-incentives",
            type: "GET",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },

            success: function (response) {
                if (typeof logged_user !== 'undefined' && (logged_user === 5 || logged_user === 4 || logged_user === 3 || logged_user === 2)) {

                    // Draw the table
                    var table = drawTable("Incentives-" + activated_pane);
                    $("#general-section").empty();
                    var tbody = $("#" + "Incentives-" + activated_pane + " tbody");
                    tbody.empty(); // Clear existing data

                    $.each(response.incentives, function (index, item) {
                        var officerDetails = item.officer_details;
                        var incentivesDetails = item.incentive;
                        if (activated_pane == "general") {
                            var row = [
                                index,
                                officerDetails.names,
                                Number(incentivesDetails.outstanding_principal_individual ?? 0).toLocaleString(),
                                Number(incentivesDetails.outstanding_principal_group ?? 0).toLocaleString() ?? 0,
                                Number(incentivesDetails.outstanding_principal_sgl ?? 0).toLocaleString() ?? 0,
                                Number(incentivesDetails.unique_customer_id_individual ?? 0).toLocaleString() ?? 0,
                                Number(incentivesDetails.records_for_unique_group_id_group ?? 0).toLocaleString() ?? 0,
                                incentivesDetails.sgl_records ?? 0,
                                incentivesDetails.records_for_PAR,
                                incentivesDetails.monthly_loan_loss_rate,
                                Number(incentivesDetails.sgl_records ?? 0).toLocaleString(),
                                Number(incentivesDetails.incentive_amount_PAR).toLocaleString(),
                                Number(incentivesDetails.incentive_amount_Net_Portifolio_Growth).toLocaleString() ?? 0,
                                Number(incentivesDetails.incentive_amount_Net_Client_Growth ?? 0).toLocaleString(),
                                Number(incentivesDetails.incentive_number_of_sgl_groups ?? 0).toLocaleString(),
                                Number(incentivesDetails.total_incentive_amount).toLocaleString() ?? 0,
                            ];
                        } else if (activated_pane == "individual") {
                            //check if the record is in the individual pane by checking if the outstanding_principal_individual is not null
                            if (incentivesDetails.incentive_type == "individual") {
                                var row = [
                                    index,
                                    officerDetails.names,
                                    Number(incentivesDetails.outstanding_principal_individual ?? 0).toLocaleString(),
                                    Number(incentivesDetails.unique_customer_id_individual ?? 0).toLocaleString() ?? 0,
                                    incentivesDetails.records_for_PAR,
                                    incentivesDetails.monthly_loan_loss_rate,
                                    Number(incentivesDetails.incentive_amount_PAR).toLocaleString(),
                                    Number(incentivesDetails.incentive_amount_Net_Portifolio_Growth).toLocaleString() ?? 0,
                                    Number(incentivesDetails.incentive_amount_Net_Client_Growth ?? 0).toLocaleString(),
                                    Number(incentivesDetails.total_incentive_amount).toLocaleString() ?? 0,
                                ];
                            } else {
                                return;
                            }
                        } else if (activated_pane == "groups") {
                            //check if the record is in the group pane by checking if the outstanding_principal_group is not null
                            if (incentivesDetails.incentive_type == "group") {
                                var row = [
                                    index,
                                    officerDetails.names,
                                    Number(incentivesDetails.outstanding_principal_group ?? 0).toLocaleString() ?? 0,
                                    Number(incentivesDetails.records_for_unique_group_id_group ?? 66666).toLocaleString() ?? 0,
                                    incentivesDetails.records_for_PAR,
                                    incentivesDetails.monthly_loan_loss_rate,
                                    Number(incentivesDetails.incentive_amount_PAR).toLocaleString(),
                                    Number(incentivesDetails.incentive_amount_Net_Portifolio_Growth).toLocaleString() ?? 0,
                                    Number(incentivesDetails.incentive_amount_Net_Client_Growth ?? 0).toLocaleString(),
                                    Number(incentivesDetails.total_incentive_amount).toLocaleString() ?? 0,
                                ];
                            } else {
                                return;
                            }
                        } else if (activated_pane == "fast") {
                            //check if the record is in the fast pane by checking if the outstanding_principal_sgl is not null
                            if (incentivesDetails.incentive_type == "fast") {
                                var row = [
                                    index,
                                    officerDetails.names,
                                    Number(incentivesDetails.outstanding_principal_sgl ?? 0).toLocaleString() ?? 0,
                                    incentivesDetails.records_for_PAR,
                                    incentivesDetails.monthly_loan_loss_rate,
                                    Number(incentivesDetails.sgl_records ?? 0).toLocaleString(),
                                    Number(incentivesDetails.incentive_amount_PAR).toLocaleString(),
                                    Number(incentivesDetails.incentive_amount_Net_Portifolio_Growth).toLocaleString() ?? 0,
                                    Number(incentivesDetails.incentive_amount_Net_Client_Growth ?? 0).toLocaleString(),
                                    Number(incentivesDetails.incentive_number_of_sgl_groups ?? 0).toLocaleString(),
                                    Number(incentivesDetails.total_incentive_amount).toLocaleString() ?? 0,
                                ];
                            } else {
                                return;
                            }
                        }
                        table.row.add(row).draw();
                    });

                    // Stop the spinner and display content
                    $("#general-section").show();
                    $("#table-section").show();
                    $("#spinner").hide();
                } else {
                    // Clear existing content
                    $("#general-section").empty();
                    if (response.incentives.length === 0) {
                        $("#general-section").append(`
                            <div class="card bg-orange rounded-lg shadow">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-uppercase font-weight-bold">No incentives available</h5>
                                </div>
                            </div>
                        `);
                    } else {
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
                            <p class="card-text"><strong>Outstanding principal (Individual):</strong> ${(parseFloat(incentivesDetails.outstanding_principal_individual ?? 0)).toLocaleString() ?? 0}/=</p>
                            <p class="card-text"><strong>Outstanding principal (Group):</strong> ${(parseFloat(incentivesDetails.outstanding_principal_group ?? 0)).toLocaleString() ?? 0}/=</p>
                            <p class="card-text"><strong>Outstanding principal (SGL):</strong> ${(parseFloat(incentivesDetails.outstanding_principal_sgl ?? 0)).toLocaleString() ?? 0}/=</p>
                            <p class="card-text"><strong>Number of Customers(Individual):</strong> ${Number(incentivesDetails.unique_customer_id_individual ?? 0).toLocaleString() ?? 0}</p>
                            <p class="card-text"><strong>Number of Customers(Group):</strong> ${Number(incentivesDetails.records_for_unique_group_id_group ?? 0).toLocaleString() ?? 0}</p>
                            <p class="card-text"><strong>PAR>1Day:</strong> ${incentivesDetails.records_for_PAR ?? 0}%</p>
                            <p class="card-text"><strong>Monthly Loan Loss Rate:</strong> ${incentivesDetails.monthly_loan_loss_rate ?? 0}%</p>
                            <p class="card-text"><strong>Number Of Groups:</strong> ${incentivesDetails.sgl_records != undefined ? (incentivesDetails.sgl_records).toLocaleString() : 0}</p>
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
