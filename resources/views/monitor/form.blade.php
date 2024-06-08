<div class="form-group">
    <label for="name" class="control-label">{{ 'Name' }}</label>
    <input class="form-control" name="name" type="text" id="name"
        value="{{ isset($monitor->name) ? $monitor->name : '' }}">
</div>
<div class="form-group">
    <label for="location" class="control-label">{{ 'Location' }}</label>
    <input class="form-control" name="location" type="text" id="location"
        value="{{ isset($monitor->location) ? $monitor->location : '' }}">
</div>
<div class="form-group">
    <label for="phone" class="control-label">{{ 'Phone' }}</label>
    <input class="form-control" name="phone" type="phone" id="phone"
        value="{{ isset($monitor->phone) ? $monitor->phone : '' }}">
</div>
<div class="form-group mb-5">
    <label for="monitor" class="control-label">{{ 'Activity' }}</label>
    <select class="form-control" name="activity" id="monitor">
        @foreach (json_decode('{"1":"Marketing","2":"Appraisal","3":"Application", "4":"Verification", "5":"Monitoring", "6":"Other"}') as $item => $value)
            <option value="{{ $value }}" {{ isset($monitor->monitor) && $monitor->monitor == $item ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>
<div class="form-group" id="otherActivity" style="display: none;">
    <input class="form-control" name="other_activity" type="text" id="other" maxlength="30" placeholder="Enter name of your activity">
</div>
<div class="form-group">
    <input type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#monitor').change(function() {
            if ($(this).val() === '6') {
                $('#otherActivity').show();
            } else {
                $('#otherActivity').hide();
            }
        });
    });
</script>
