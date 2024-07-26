@extends('layouts.user_type.auth')

@section('content')
    <div class="container text-center">
        <nav class="">
            <div class="nav nav-tabs nav-fill nav-underline" id="nav-tab" role="tablist">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button"
                    role="tab" aria-controls="general" aria-selected="true">General</button>
                <button class="nav-link" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual"
                    type="button" role="tab" aria-controls="individual" aria-selected="false">Individual</button>
                <button class="nav-link" id="groups-tab" data-bs-toggle="tab" data-bs-target="#groups" type="button"
                    role="tab" aria-controls="groups" aria-selected="false">Groups</button>
                <button class="nav-link" id="fast-tab" data-bs-toggle="tab" data-bs-target="#fast" type="button"
                    role="tab" aria-controls="fast" aria-selected="false">Fast</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab"
                tabindex="0">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title
                            ">General Settings</h4>
                                {{-- add a form for incentive settings --}}
                                <form action="{{ route('incentive-settings.store') }}" method="POST">
                                    @method('PATCH')
                                    @csrf
                                    {{-- bootstrap form legend showing PAR calculation settings ie maximum par, percentage incentive --}}
                                    <fieldset class="form-group border p-3">
                                        <legend>PAR Calculation Settings</legend>
                                        <div class="form-group row">
                                            <label for="max_par" class="col-md-4 col-form-label text-md-right">Maximum
                                                PAR</label>
                                            <div class="col-md-6">
                                                <input type="number" step="any" name="max_par" id="max_par"
                                                    class="form-control"
                                                    value="{{ number_format($incentiveSettings->max_par, 2, '.', ',') }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="percentage_incentive_par"
                                                class="col-md-4 col-form-label text-md-right">Percentage Incentive</label>
                                            <div class="col-md-6">
                                                <input type="text" name="percentage_incentive_par"
                                                    id="percentage_incentive_par"
                                                    value="{{ number_format($incentiveSettings->percentage_incentive_par, 2, '.', ',') }}"
                                                    class="form-control" required>
                                            </div>
                                        </div>
                                    </fieldset>
                                    {{-- bootstrap form legend showing Net Portifolio Growth ie Maximum cap, minimum cap, percentage incentive --}}
                                    <fieldset class="form-group border p-3">
                                        <legend>Net Portifolio Growth Settings</legend>
                                        <div class="form-group row">
                                            <label for="max_cap_portifolio"
                                                class="col-md-4 col-form-label text-md-right">Maximum
                                                Cap</label>
                                            <div class="col-md-6">
                                                <input type="text" name="max_cap_portifolio" id="max_cap"
                                                    class="form-control number_format"
                                                    value="{{ number_format($incentiveSettings->max_cap_portifolio, 2, '.', ',') }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="min_cap_portifolio"
                                                class="col-md-4 col-form-label text-md-right">Minimum
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
                                    <fieldset class="form-group border p-3">
                                        <legend>Net Client Growth Settings</legend>
                                        <div class="form-group row">
                                            <label for="max_cap_client"
                                                class="col-md-4 col-form-label text-md-right">Maximum
                                                Cap</label>
                                            <div class="col-md-6">
                                                <input type="text" name="max_cap_client"
                                                    id="max_cap_client number_format" class="form-control"
                                                    value="{{ number_format($incentiveSettings->max_cap_client, 0, '.', ',') }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="min_cap_client"
                                                class="col-md-4 col-form-label text-md-right">Minimum
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
                                    <fieldset class="form-group border p-3">
                                        <legend>Maximum Incentive Settings</legend>
                                        <div class="form-group row">
                                            <label for="max_incentive"
                                                class="col-md-4 col-form-label text-md-right">Maximum
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
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="individual" role="tabpanel" aria-labelledby="individual-tab" tabindex="0">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title
                            ">Individual Settings</h4>
                                {{-- add a form for incentive settings --}}
                                <form action="{{ route('incentive-settings.store') }}" method="POST">
                                    @method('PATCH')
                                    @csrf
                                    <div class="form-group row">
                                        <label for="max_cap_portifolio_individual"
                                            class="col-md-4 col-form-label text-md-right">Outstanding Principal</label>
                                        <div class="col-md-6">
                                            <input type="text" name="max_cap_portifolio_individual"
                                                id="max_cap_portifolio_individual" class="form-control number_format"
                                                value="{{ number_format($incentiveSettings->max_cap_portifolio_individual, 2, '.', ',') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="min_cap_client_individual"
                                            class="col-md-4 col-form-label text-md-right">Minimum Number Of Clients</label>
                                        <div class="col-md-6">
                                            <input type="number" step="any" name="min_cap_client_individual"
                                                id="min_cap_client_individual" class="form-control"
                                                value="{{ number_format($incentiveSettings->min_cap_client_individual, 2, '.', ',') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="max_par_individual"
                                            class="col-md-4 col-form-label text-md-right">Maximum
                                            PAR</label>
                                        <div class="col-md-6">
                                            <input type="number" step="any" name="max_par_individual"
                                                id="max_par_individual" class="form-control"
                                                value="{{ number_format($incentiveSettings->max_par_individual, 2, '.', ',') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="max_llr_individual"
                                            class="col-md-4 col-form-label text-md-right">Maximum LLR</label>
                                        <div class="col-md-6">
                                            <input type="text" name="max_llr_individual" id="max_llr_individual"
                                                value="{{ number_format($incentiveSettings->max_llr_individual, 2, '.', ',') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>

                                    {{-- submit button --}}
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Save') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="groups" role="tabpanel" aria-labelledby="groups-tab" tabindex="0">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title
                            ">Group Settings</h4>
                                {{-- add a form for incentive settings --}}
                                <form action="{{ route('incentive-settings.store') }}" method="POST">
                                    @method('PATCH')
                                    @csrf
                                    <div class="form-group row">
                                        <label for="max_cap_portifolio_group"
                                            class="col-md-4 col-form-label text-md-right">Outstanding Principal</label>
                                        <div class="col-md-6">
                                            <input type="text" name="max_cap_portifolio_group"
                                                id="max_cap_portifolio_group" class="form-control number_format"
                                                value="{{ number_format($incentiveSettings->max_cap_portifolio_group, 2, '.', ',') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="min_cap_client_group"
                                            class="col-md-4 col-form-label text-md-right">Minimum Number Of Clients</label>
                                        <div class="col-md-6">
                                            <input type="number" step="any" name="min_cap_client_group"
                                                id="min_cap_client_group" class="form-control"
                                                value="{{ number_format($incentiveSettings->min_cap_client_group, 2, '.', ',') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="max_par_group" class="col-md-4 col-form-label text-md-right">Maximum
                                            PAR</label>
                                        <div class="col-md-6">
                                            <input type="number" step="any" name="max_par_group" id="max_par_group"
                                                class="form-control"
                                                value="{{ number_format($incentiveSettings->max_par_group, 2, '.', ',') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="max_llr_group" class="col-md-4 col-form-label text-md-right">Maximum
                                            LLR</label>
                                        <div class="col-md-6">
                                            <input type="text" name="max_llr_group" id="max_llr_group"
                                                value="{{ number_format($incentiveSettings->max_llr_group, 2, '.', ',') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>

                                    {{-- submit button --}}
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Save') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="fast" role="tabpanel" aria-labelledby="fast-tab" tabindex="0">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title
                            ">Fast Settings</h4>
                                {{-- add a form for incentive settings --}}
                                <form action="{{ route('incentive-settings.store') }}" method="POST">
                                    @method('PATCH')
                                    @csrf
                                    <div class="form-group row">
                                        <label for="max_par_fast" class="col-md-4 col-form-label text-md-right">Maximum
                                            PAR</label>
                                        <div class="col-md-6">
                                            <input type="number" step="any" name="max_par_fast" id="max_par_fast"
                                                class="form-control"
                                                value="{{ number_format($incentiveSettings->max_par_fast, 2, '.', ',') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="max_llr_fast" class="col-md-4 col-form-label text-md-right">Maximum
                                            LLR</label>
                                        <div class="col-md-6">
                                            <input type="text" name="max_llr_fast" id="max_llr_fast"
                                                value="{{ number_format($incentiveSettings->max_llr_fast, 2, '.', ',') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="max_cap_number_of_groups_fast"
                                            class="col-md-4 col-form-label text-md-right">Maximum Number Of Groups</label>
                                        <div class="col-md-6">
                                            <input type="text" name="max_cap_number_of_groups_fast"
                                                id="max_cap_number_of_groups_fast"
                                                value="{{ number_format($incentiveSettings->max_cap_number_of_groups_fast, 0, '.', ',') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="min_cap_number_of_groups_fast"
                                            class="col-md-4 col-form-label text-md-right">Minimum Number Of Groups</label>
                                        <div class="col-md-6">
                                            <input type="text" name="min_cap_number_of_groups_fast"
                                                id="min_cap_number_of_groups_fast"
                                                value="{{ number_format($incentiveSettings->min_cap_number_of_groups_fast, 0, '.', ',') }}"
                                                class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="max_cap_portifolio_fast"
                                            class="col-md-4 col-form-label text-md-right">Maximum
                                            Cap</label>
                                        <div class="col-md-6">
                                            <input type="text" name="max_cap_portifolio_fast"
                                                id="max_cap_portifolio_fast" class="form-control number_format"
                                                value="{{ number_format((float) $incentiveSettings->max_cap_portifolio_fast, 2, '.', ',') }}"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="min_cap_portifolio_fast"
                                            class="col-md-4 col-form-label text-md-right">Minimum
                                            Cap</label>
                                        <div class="col-md-6">
                                            <input type="text" name="min_cap_portifolio_fast"
                                                id="min_cap_portifolio_fast" class="form-control number_format"
                                                value="{{ number_format($incentiveSettings->min_cap_portifolio_fast, 2, '.', ',') }}"
                                                required>
                                        </div>
                                    </div>
                                    {{-- submit button --}}
                                    <div class="form-group row mb-0">
                                        <div class="col-md-6 offset-md-4">
                                            <button type="submit" class="btn btn-primary">
                                                {{ __('Save') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
