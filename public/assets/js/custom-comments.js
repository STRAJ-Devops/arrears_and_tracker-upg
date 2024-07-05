$(document).ready(function () {

    $("#comments-table").DataTable({
        processing: true,
        serverSide: false,
        drawCallback: function () {
            $("#content").show();
            $("#spinner").hide();
        },
        //export buttons
        dom: "Bfrtip",
        //style the buttons
        buttons: [
            {
                extend: "csv",
                className: "btn btn-warning btn-small text-white",
                messageTop: "Comments about Arrears",
            },
            {
                extend: "excel",
                className: "btn btn-warning btn-small text-white",
                messageTop: "Comments about Arrears",
            },
            {
                extend: "pdf",
                className: "btn btn-warning btn-small text-white",
                messageTop: "Comments about Arrears",
                customize: function (doc) {
                    doc.styles.tableHeader.fillColor = '#FFA500';
                }
            },
            {
                extend: "print",
                className: "btn btn-warning btn-small text-white",
                messageTop: "Comments about Arrears",
            },
        ],
        ajax: {
            // Use template literals (preferred)
            url: "/get-all-comments",
            type: "GET",
            dataSrc: "comments",
        },
        columns: [
            {
                data: "id",
            },
            {
                data: "customer.customer_id",
            },
            {
                data: "customer.names",
            },
            {
                data: "comment",
            },
            {
                data: "number_of_days_late"
            },
            {
                // Define a custom render function for the date column
                data: "created_at",
                render: function (data, type, row) {
                    return formatDate(data);
                },
            },
        ],
    });

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
