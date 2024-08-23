<div class="form-group">
    <label for="staff_id" class="control-label">{{ 'Officer ID' }}</label>
    <input class="form-control" name="staff_id" type="text" id="staff_id"
        value="{{ isset($user->staff_id) ? $user->staff_id : '' }}">

    @if ($errors->has('staff_id'))
        <span class="text-danger">{{ $errors->first('staff_id') }}</span>
    @endif

</div>

<div class="form-group">
    <label for="names" class="control-label">{{ 'Full Name' }}</label>
    <input class="form-control" name="names" type="text" id="names"
        value="{{ isset($user->names) ? $user->names : '' }}">

    @if ($errors->has('names'))
        <span class="text-danger">{{ $errors->first('names') }}</span>
    @endif
</div>

<div class="form-group">
    <label for="username" class="control-label">{{ 'Username' }}</label>
    <input class="form-control" name="username" type="text" id="username"
        value="{{ isset($user->username) ? $user->username : '' }}">

    @if ($errors->has('username'))
        <span class="text-danger">{{ $errors->first('username') }}</span>
    @endif
</div>

<div class="form-group">
    <label for="user_type" class="control-label">{{ 'Role' }}</label>
    <select class="form-control" name="user_type" id="user_type">
        @foreach (json_decode('{"1":"Credit Officer","2":"Branch Manager","3":"Regional Manager","4":"Head Office","5":"IT Admin"}') as $item => $value)
            <option value="{{ $item }}" {{ isset($user->role) && $user->role == $item ? 'selected' : '' }}>
                {{ $value }}</option>
        @endforeach
    </select>
</div>

{{-- create a drop down for regions --}}
<div class="form-group" id="regionDropdown">
    <label for="region_id" class="control-label">{{ 'Region' }}</label>
    <select class="form-control shadow-none" name="region_id" id="region_id">
        @foreach ($regions as $region)
            <option value="{{ $region->region_id }}"
                {{ isset($user->region_id) && $user->region_id == $region->region_id ? 'selected' : '' }}>
                {{ $region->region_name }}</option>
        @endforeach
    </select>
</div>

{{-- create a drop down for branches --}}
<div class="form-group" id="branchDropdown">
    <label for="branch_id" class="control-label">{{ 'Branch' }}</label>
    <select class="form-control shadow-none" name="branch_id" id="branch_id">
        @foreach ($branches as $branch)
            <option value="{{ $branch->branch_id }}"
                {{ isset($user->branch_id) && $user->branch_id == $branch->branch_id ? 'selected' : '' }}>
                {{ $branch->branch_name }}</option>
        @endforeach
    </select>
</div>

<div class="form-group">
    <label for="password" class="control-label">{{ 'Password' }}</label>
    <input class="form-control" name="password" type="text" id="password"
        value="{{ isset($user->un_hashed_password) ? $user->un_hashed_password : '' }}">
    @if ($errors->has('password'))
        <span class="text-danger">{{ $errors->first('password') }}</span>
    @endif
</div>
<div class="form-group">
    <input class="button4" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

@push('dashboard')
    <script>
        $(document).ready(function() {
            console.log('ready');
            // hide the region dropdown initially
            $('#regionDropdown').hide();
            $('#user_type').on('change', function() {
                var role = $(this).val();

                // Hide all dropdowns initially
                $('#branchDropdown').hide();
                $('#regionDropdown').hide();

                if (role == 1 || role == 2) {
                    //hide region dropdown if role is 1 or 2 and show branch dropdown
                    $('#branchDropdown').show();

                    //if region dropdown is visible, hide it
                    $('#regionDropdown').hide();

                    //remove the drop down from the form
                    $('#region_id').remove();
                } else if (role == 3) {
                    //show region dropdown if role is 3
                    $('#regionDropdown').show();

                    //if branch dropdown is visible, hide it
                    $('#branchDropdown').hide();

                    //remove the drop down from the form
                    $('#branch_id').remove();
                } else {
                    //hide both dropdowns if role is not 1, 2 or 3
                    $('#branchDropdown').hide();
                    $('#regionDropdown').hide();

                    //remove the drop down from the form
                    $('#branch_id').remove();
                    $('#region_id').remove();
                }
            });
        });
    </script>
@endpush
