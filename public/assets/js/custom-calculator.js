(function ($) {
    $.fn.jasc = function (options) {

        var settings = $.extend({/* Defaults */ }, options);

        var controlsHtml = `<div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="loan">
                    <h3>Loan Details</h3>
                    <div class="form-group">
                        <label for="amortization-schedule-amount">Loan Amount</label>
                        <div class="input-group">
                            <input type="text" class="form-control number_format shadow-none" id="amortization-schedule-amount" placeholder="e.g. 3800" aria-describedby="basic-addon2">
                            <span class="input-group-text" id="basic-addon2">UGX</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="amortization-schedule-interest">Annual Interest</label>
                        <div class="input-group">
                            <input type="text" class="form-control number_format shadow-none" id="amortization-schedule-interest" placeholder="e.g. 32" aria-describedby="basic-addon3">
                            <span class="input-group-text" id="basic-addon3">%</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="amortization-schedule-duration">Duration</label>
                        <div class="input-group">
                            <input type="text" class="form-control number_format shadow-none" id="amortization-schedule-duration" placeholder="e.g. 48" aria-describedby="basic-addon4">
                            <span class="input-group-text" id="basic-addon4">Month(s)</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="amortization-schedule-grace">Grace Period</label>
                        <div class="input-group">
                            <input type="text" class="form-control number_format shadow-none" id="amortization-schedule-grace" placeholder="e.g. 0" aria-describedby="basic-addon5">
                            <span class="input-group-text" id="basic-addon5">Month(s)</span>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-md-12">
                <div class="date">
                    <h3>Loan Start Date</h3>
                    <div class="form-group">
                        <label for="amortization-schedule-month">Start Month</label>
                        <input type="text" class="form-control border rounded-3 shadow-none" id="amortization-schedule-month" placeholder="e.g. 12">
                    </div>
                    <div class="form-group">
                        <label for="amortization-schedule-day">Start Day</label>
                        <input type="text" class="form-control border rounded-3 shadow-none" id="amortization-schedule-day" placeholder="e.g. 28">
                    </div>
                    <div class="form-group">
                        <label for="amortization-schedule-year">Start Year</label>
                        <input type="text" class="form-control border rounded-3 shadow-none" id="amortization-schedule-year" placeholder="e.g. 2014">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="btn btn-warning btn-lg text-light" id="amortization-schedule-submit">Calculate</button>
            </div>
        </div>
    </div>
    `;
        $('#' + settings.controlsID).append(controlsHtml);

        //set the commas padding in the input fields
        $('.number_format').on('keyup', function (event) {
            var selection = window.getSelection().toString();

            if (selection !== '') {
                return;
            }

            if ($.inArray(event.keyCode, [38, 40, 37, 39]) !== -1) {
                return;
            }

            var $this = $(this);

            var input = $this.val();

            // Remove non-numeric and non-decimal characters except periods and decimals
            input = input.replace(/[^\d.]+/g, "");

            // Convert input to a numeric value
            var numericValue = parseFloat(input);

            // If input is NaN, default to 0
            numericValue = isNaN(numericValue) ? 0 : numericValue;

            $this.val(function () {
                var pos = this.selectionStart;

                // Format the numeric value as a string with commas and two decimal places
                var formattedInput = numericValue.toLocaleString("en-US", {
                });

                // Update the input field value with the formatted number
                this.value = formattedInput;

                // Set the cursor position after the last modified character
                this.setSelectionRange(pos, pos);

                // Return the formatted input
                return formattedInput;
            });
        });

        var container = this;
        $('#amortization-schedule-submit').on('click', function () {
            var dataHtml = calculate();
            if (dataHtml) {
                // Insert into dom
                $(container).find('table').remove();
                $(container).append(dataHtml.join(''));
            }
            return false;
        });

        function calculate() {
            var loanAmount = parseInt($('#amortization-schedule-amount').val().replace(/,/g, '')),
                loanInterest = parseFloat($('#amortization-schedule-interest').val()), // %
                loanDuration = parseInt($('#amortization-schedule-duration').val()), // Month
                loanGrace = parseInt($('#amortization-schedule-grace').val()), // Month
                loanInitMonth = parseInt($('#amortization-schedule-month').val()),
                loanInitDay = parseInt($('#amortization-schedule-day').val()),
                loanInitYear = parseInt($('#amortization-schedule-year').val());

            // validate input
            if (isNaN(loanAmount) || isNaN(loanInterest) || isNaN(loanDuration) || isNaN(loanGrace) || isNaN(loanInitMonth) || isNaN(loanInitDay) || isNaN(loanInitYear) ||
                loanAmount < 1 || loanInterest < 0 || loanInterest > 100 || loanDuration < 1 || loanGrace < 0 || loanInitMonth < 0 || loanInitMonth > 12 || loanInitDay < 0 || loanInitDay > 31 || loanInitYear < 1000 || loanInitYear > 9999) {
                if (isNaN(loanAmount) || loanAmount < 1) { $('#amortization-schedule-amount').addClass('error'); } else { $('#amortization-schedule-amount').removeClass('error'); }
                if (isNaN(loanInterest) || loanInterest < 0 || loanInterest > 100) { $('#amortization-schedule-interest').addClass('error'); } else { $('#amortization-schedule-interest').removeClass('error'); }
                if (isNaN(loanDuration) || loanDuration < 1) { $('#amortization-schedule-duration').addClass('error'); } else { $('#amortization-schedule-duration').removeClass('error'); }
                if (isNaN(loanGrace) || loanGrace < 0) { $('#amortization-schedule-grace').addClass('error'); } else { $('#amortization-schedule-grace').removeClass('error'); }
                if (isNaN(loanInitMonth) || loanInitMonth < 0 || loanInitMonth > 12) { $('#amortization-schedule-month').addClass('error'); } else { $('#amortization-schedule-month').removeClass('error'); }
                if (isNaN(loanInitDay) || loanInitDay < 0 || loanInitDay > 31) { $('#amortization-schedule-day').addClass('error'); } else { $('#amortization-schedule-day').removeClass('error'); }
                if (isNaN(loanInitYear) || loanInitYear < 1000 || loanInitYear > 9999) { $('#amortization-schedule-year').addClass('error'); } else { $('#amortization-schedule-year').removeClass('error'); }
                return false;
            } else {
                $.each($('#' + settings.controlsID).find('input'), function (key, val) {
                    $(val).removeClass('error');
                });
            }

            var loanStart = new Date(loanInitYear, loanInitMonth, loanInitDay);
            var loanTable = [];

            // Create array of the schedule
            var i = 0;
            for (var m = loanInitMonth; m < loanDuration + loanInitMonth; m++) {
                // date
                var d = new Date(loanStart.getTime());
                d.setMonth(m);
                // percent
                if (i > loanGrace) {
                    var pmt = PMT((loanInterest / 100) / 12, loanDuration - loanGrace, loanAmount);
                    var ipmt = IPMT(loanAmount, pmt, (loanInterest / 100) / 12, i - loanGrace);
                    var p = -ipmt;
                } else {
                    var p = loanAmount * (loanInterest / 100) / 12
                }
                // installment
                if (i >= loanGrace) {
                    var inst = -PMT((loanInterest / 100) / 12, loanDuration - loanGrace, loanAmount);
                } else {
                    var inst = p;
                }
                // base
                var b = inst - p;
                // balance
                if (i == 0) {
                    var c = loanAmount - b;
                } else {
                    var c = loanTable[i - 1].balance - b;
                }
                // hash
                loanTable.push({ date: d, base: b, percent: p, installment: inst, balance: c });
                //
                i++;
            }
            if ($.fn.DataTable.isDataTable('#loan-schedule')) {
                //remove the table
                $('#loan-schedule').DataTable().destroy();
            }
            createDataTable(loanTable);
        }

        function createDataTable(data) {
            var table = $('#loan-schedule').DataTable({
                paging: false,
                data: data,
                dom: 'Bfrtip',
                order: [[4, 'desc']],
                columns: [
                    { data: 'date', title: 'Date', render: function (data) { return formatDate(data); } },
                    { data: 'base', title: 'Principal', render: function (data) { return data.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } },
                    { data: 'percent', title: 'Interest', render: function (data) { return data.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } },
                    { data: 'installment', title: 'Installment', render: function (data) { return data.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } },
                    { data: 'balance', title: 'Balance', render: function (data) { return data.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); } }
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
                    $(api.column(1).footer()).html(sum(api.column(1).data())); // Principle Arrears
                    $(api.column(2).footer()).html(sum(api.column(2).data())); // Principle Arrears
                    $(api.column(3).footer()).html(sum(api.column(3).data())); // Interest Arrears
                },
            });

        }

        // Helper functions
        function formatDate(date) {
            return String(date).substr(4, 12);
        }
        function roundTo2(num) {
            return parseFloat(Math.round(num * 100) / 100).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }


        function round2null(num) {
            return parseFloat(Math.round(num * 100) / 100).toFixed(0);
        }
        function PMT(rate, nper, pv, fv, type) {
            // PMT excel function taken from https://gist.github.com/pies/4166888
            if (!fv) fv = 0;
            if (!type) type = 0;
            if (rate == 0) return -(pv + fv) / nper;
            var pvif = Math.pow(1 + rate, nper);
            var pmt = rate / (pvif - 1) * -(pv * pvif + fv);
            if (type == 1) {
                pmt /= (1 + rate);
            };
            return pmt;
        }
        function IPMT(pv, pmt, rate, per) {
            // PMT excel function taken from https://gist.github.com/pies/4166888
            var tmp = Math.pow(1 + rate, per);
            return 0 - (pv * tmp * rate + pmt * (tmp - 1));
        }

    }
}(jQuery));
