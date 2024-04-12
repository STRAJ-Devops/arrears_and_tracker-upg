@extends('layouts.user_type.auth')

@section('content')
    <div class="container text-center">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title
                    ">Incentive Settings</h4>
                        {{-- add a form for incentive settings --}}
                        <form action="{{ route('incentive-settings.store') }}" method="POST">
                            @method('PATCH')
                            @csrf
                            {{-- bootstrap form legend showing PAR calculation settings ie maximum par, percentage incentive --}}
                            <fieldset>
                                <legend>PAR Calculation Settings</legend>
                                <div class="form-group row">
                                    <label for="max_par" class="col-md-4 col-form-label text-md-right">Maximum PAR</label>
                                    <div class="col-md-6">
                                        <input type="number" name="max_par" id="max_par" class="form-control"
                                            value="{{ number_format($incentiveSettings->max_par, 2, '.', ',') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="percentage_incentive_par"
                                        class="col-md-4 col-form-label text-md-right">Percentage Incentive</label>
                                    <div class="col-md-6">
                                        <input type="text" name="percentage_incentive_par" id="percentage_incentive_par"
                                            value="{{ number_format($incentiveSettings->percentage_incentive_par, 2, '.', ',') }}"
                                            class="form-control" required>
                                    </div>
                                </div>
                            </fieldset>
                            {{-- bootstrap form legend showing Net Portifolio Growth ie Maximum cap, minimum cap, percentage incentive --}}
                            <fieldset>
                                <legend>Net Portifolio Growth</legend>
                                <div class="form-group row">
                                    <label for="max_cap_portifolio" class="col-md-4 col-form-label text-md-right">Maximum
                                        Cap</label>
                                    <div class="col-md-6">
                                        <input type="text" name="max_cap_portifolio" id="max_cap"
                                            class="form-control number_format"
                                            value="{{ number_format($incentiveSettings->max_cap_portifolio, 2, '.', ',') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="min_cap_portifolio" class="col-md-4 col-form-label text-md-right">Minimum
                                        Cap</label>
                                    <div class="col-md-6">
                                        <input type="text" name="min_cap_portifolio" id="min_cap_portifolio"
                                            class="form-control number_format"
                                            value="{{ number_format($incentiveSettings->min_cap_portifolio, 2, '.', ',') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="percentage_incentive_portifolio"
                                        class="col-md-4 col-form-label text-md-right">Percentage Incentive</label>
                                    <div class="col-md-6">
                                        <input type="text" name="percentage_incentive_portifolio"
                                            id="percentage_incentive_portifolio" class="form-control"
                                            value="{{ number_format($incentiveSettings->percentage_incentive_portifolio, 2, '.') }}"
                                            required>
                                    </div>
                                </div>
                            </fieldset>

                            {{-- bootstrap form legend showing Net Client Growth ie Maximum cap, minimum cap, percentage incentive --}}
                            <fieldset>
                                <legend>Net Client Growth</legend>
                                <div class="form-group row">
                                    <label for="max_cap_client" class="col-md-4 col-form-label text-md-right">Maximum
                                        Cap</label>
                                    <div class="col-md-6">
                                        <input type="text" name="max_cap_client" id="max_cap_client number_format"
                                            class="form-control"
                                            value="{{ number_format($incentiveSettings->max_cap_client, 0, '.', ',') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="min_cap_client" class="col-md-4 col-form-label text-md-right">Minimum
                                        Cap</label>
                                    <div class="col-md-6">
                                        <input type="text" name="min_cap_client" id="min_cap_client"
                                            class="form-control number_format"
                                            value="{{ number_format($incentiveSettings->min_cap_client, 0, '.', ',') }}"
                                            required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="percentage_incentive"
                                        class="col-md-4 col-form-label text-md-right">Percentage Incentive</label>
                                    <div class="col-md-6">
                                        <input type="text" name="percentage_incentive_client"
                                            id="percentage_incentive_client" class="form-control"
                                            value="{{ number_format($incentiveSettings->percentage_incentive_client, 2, '.', ',') }}"
                                            required>
                                    </div>
                                </div>
                            </fieldset>
                            {{-- Maximum incentive --}}
                            <fieldset>
                                <legend>Maximum Incentive</legend>
                                <div class="form-group row">
                                    <label for="max_incentive" class="col-md-4 col-form-label text-md-right">Maximum
                                        Incentive</label>
                                    <div class="col-md-6">
                                        <input type="text" name="max_incentive" id="max_incentive"
                                            class="form-control number_format"
                                            value="{{ number_format($incentiveSettings->max_incentive, 2, '.', ',') }}"
                                            required>
                                    </div>
                                </div>
                            </fieldset>

                            {{-- submit button --}}
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Save') }}
                                    </button>
                                </div>
                                @push('dashboard')
                                    <script>
                                        $(document).ready(function() {
                                            // Add event listener to input fields
                                            $('.number_format').on('keyup', function(event) {
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

                                                $this.val(function() {
                                                    var pos = this.selectionStart;

                                                    // Format the numeric value as a string with commas and two decimal places
                                                    var formattedInput = numericValue.toLocaleString("en-US", {
                                                        style: "decimal",
                                                        maximumFractionDigits: 2,
                                                        minimumFractionDigits: 2
                                                    });

                                                    // Update the input field value with the formatted number
                                                    this.value = formattedInput;

                                                    // Set the cursor position after the last modified character
                                                    this.setSelectionRange(pos, pos);

                                                    // Return the formatted input
                                                    return formattedInput;
                                                });
                                            });

                                        });
                                    </script>
                                @endpush
                            @endsection
