$(document).ready(function () {
    var table;
    table = getMonitors();
    var tabEl = document.querySelectorAll('button[data-bs-toggle="pill"]');
    for (i = 0; i < tabEl.length; i++) {
        tabEl[i].addEventListener("shown.bs.tab", function (event) {
            const activated_pane = document.querySelector(
                event.target.getAttribute("data-bs-target")
            );
            const deactivated_pane = document.querySelector(
                event.relatedTarget.getAttribute("data-bs-target")
            );
            //remove existing table
            $("#monitor-table").DataTable().destroy();
            getMonitors(activated_pane.id);
        });
    }

    // Attach event listener to the table body, and listen for clicks on buttons with class 'btn-appraise'
    $("#monitor-table tbody").on("click", "button.btn-appraise", function () {
        var id = $(this).data("appraise-id");
        // Assuming you have a URL for the POST request
        var postUrl = "/appraise"; // Change this to your actual endpoint

        // Make the POST request
        // Make an AJAX request to submit the form data
        $.ajax({
            url: postUrl, // Replace with your server endpoint
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                monitor_id: id,
            },
            success: function (response) {
                //show a swal alert
                Swal.fire({
                    title: "Success",
                    text: "The sale status updated successfully",
                    icon: "success",
                    confirmButtonText: "Ok",
                });

                //refresh the table
                $("#monitor-table").DataTable().ajax.reload();
            },
            error: function (xhr, status, error) {
                //show a swal alert
                Swal.fire({
                    title: "Error",
                    text: "Error submitting comment",
                    icon: "error",
                    confirmButtonText: "Ok",
                });
                // Handle any errors that occur during the AJAX request
                console.error("Error submitting comment:", xhr.responseText);
                // Optionally, display an error message to the user
            },
        });
    });

    // Attach event listener to the table body, and listen for clicks on buttons with class 'btn-appraise'
    $("#monitor-table tbody").on("click", "button.btn-apply", function () {
        var id = $(this).data("apply-id");
        // Assuming you have a URL for the POST request
        var postUrl = "/apply"; // Change this to your actual endpoint

        // Make the POST request
        // Make an AJAX request to submit the form data
        $.ajax({
            url: postUrl, // Replace with your server endpoint
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                monitor_id: id,
            },
            success: function (response) {
                //show a swal alert
                Swal.fire({
                    title: "Success",
                    text: "Comment added successfully",
                    icon: "success",
                    confirmButtonText: "Ok",
                });

                //refresh the table
                $("#monitor-table").DataTable().ajax.reload();
            },
            error: function (xhr, status, error) {
                //show a swal alert
                Swal.fire({
                    title: "Error",
                    text: "Error submitting comment",
                    icon: "error",
                    confirmButtonText: "Ok",
                });
                // Handle any errors that occur during the AJAX request
                console.error("Error submitting comment:", xhr.responseText);
                // Optionally, display an error message to the user
            },
        });
    });

    // Initialize datepicker
    $("#date-filter").datepicker({
        dateFormat: "M d, yy",
        onSelect: function (dateText, inst) {
            table.column(6).search(dateText).draw(); // Assuming next repayment date is in column index 10
        },
    });
    //set the initial date to today
    $("#date-filter").datepicker("setDate", new Date());

    //show the calendar
    $("#date-filter").datepicker("show");

    function getMonitors(activity = "all") {
        var table = $("#monitor-table").DataTable({
            processing: true,
            serverSide: false,
            drawCallback: function () {
                $("#content").show();
                $("#spinner").hide();
            },
            responsive: true,
            //export buttons
            dom: "Bfrtip",
            //style the buttons
            buttons: [
                {
                    extend: "csv",
                    className: "btn btn-warning btn-small",
                    messageTop: "Sales Activity",
                },
                {
                    extend: "excel",
                    className: "btn btn-warning btn-small",
                    messageTop: "Sales Activity",
                },
                {
                    extend: "pdf",
                    className: "btn btn-warning btn-small",
                    messageTop: "Sales Activity",
                    customize: function (doc) {
                        doc.styles.tableHeader.fillColor = '#FFA500';
                    }
                },
                {
                    extend: "print",
                    className: "btn btn-warning btn-small",
                    messageTop: "Sales Activity",
                },
            ],
            ajax: {
                // Use template literals (preferred)
                url: "/get-monitors",
                type: "GET",
                dataSrc: "monitors",
                data: {
                    activity: activity,
                },
            },
            columns: [
                {
                    data: "name",
                    // render a link to the monitor's profile
                    render: function (data, type, row) {
                        return '<a href="/monitor-details/' + row.id + '">' + data + "</a>";
                    },
                },
                {
                    data: "phone",
                },
                {
                    data: "location",
                },
                {
                    data: "activity",
                },
                {
                    // Define a custom render function for the date column
                    data: "created_at",
                    render: function (data, type, row) {
                        return formatDate(data);
                    },
                },
                {
                    data: "appraisal_date",
                    render: function (data, type, row) {
                        if (data) {
                            return formatDate(data);
                        } else {
                            return (
                                '<button class="btn btn-primary btn-appraise" data-appraise-id="' +
                                row.id +
                                '">Appraise</button>'
                            );
                        }
                    },
                },
                {
                    data: "application_date",
                    render: function (data, type, row) {
                        if (data) {
                            return formatDate(data);
                        } else {
                            return (
                                '<button class="btn btn-primary btn-apply" data-apply-id="' +
                                row.id +
                                '">Apply</button>'
                            );
                        }
                    },
                },
                {
                    data: "officer.names"
                }
            ],
        });

        return table;
    }

    // Function to format the date (place it outside the $(document).ready)
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
