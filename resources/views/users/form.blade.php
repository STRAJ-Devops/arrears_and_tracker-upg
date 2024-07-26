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
