@extends('layouts.user_type.auth')

@section('content')
        <div class="row">
            <div class="col-md-3">

                <div id="schedule-controls"></div>

            </div>
            <div class="col-md-9">

                <div id="schedule-container">
                    {{-- data table here --}}
                    <table id="loan-schedule" class="table table-striped">

                        {{-- footer callback --}}
                        <tbody>
                            <tfoot align="right">
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                            </tbody>
                    </table>
                </div>

            </div>
        </div>

    @push('dashboard')
        <style>
            #schedule-controls {
                margin-bottom: 45px;
            }

            #schedule-controls .loan {
                clear: both;
                overflow: hidden;
                height: 100%;
            }

            #schedule-controls .date {
                margin-bottom: 10px;
                clear: both;
                overflow: hidden;
                height: 100%;
            }

            #schedule-controls .input-group {
                margin-bottom: 4px;
            }

            #schedule-controls .input-group-addon {
                width: 111px;
                text-align: right;
            }

            #schedule-controls .input-group-addon-right {
                width: auto;
            }

            #schedule-controls .error {
                border: 1px solid red;
            }
        </style>
        <script src="{{ asset('assets/js/custom-calculator.js') }}"></script>
        <script>
            $(function() {

                $("#schedule-container").jasc({
                    controlsID: 'schedule-controls'
                });

            });
        </script>
    @endpush
@endsection
