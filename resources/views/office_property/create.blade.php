@php
    $setting = App\Models\Utility::settings();
    $plan = Utility::getChatGPTSettings();
@endphp
{{ Form::open(['url' => 'office-property', 'method' => 'post']) }}
<div class="modal-body">

    @if ($plan->enable_chatgpt == 'on')
        <div class="card-footer text-end">
            <a href="#" class="btn btn-sm btn-primary" data-size="medium" data-ajax-popup-over="true"
                data-url="{{ route('generate', ['office-property']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i>{{ __(' Generate With AI') }}
            </a>
        </div>
    @endif

    @if (\Auth::user()->type != 'employee')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('employee_id', __('Employee'), ['class' => 'col-form-label']) }}
                    {{ Form::select('employee_id', $employees, null, ['class' => 'form-control select2', 'id' => 'employee_id', 'placeholder' => __('Select Employee')]) }}
                </div>
            </div>
        </div>
    @else
        {!! Form::hidden('employee_id', !empty($employees) ? $employees->id : 0, ['id' => 'employee_id']) !!}
    @endif

    @if (request()->get('type') == null)
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('request_type', __('Request Type*'), ['class' => 'col-form-label']) }}
                    <select name="request_type" id="request_type" class="form-control select2">
                        <option value="" disabled selected>Select Request Type</option>
                        <option value="Conference/Board Room">Conference/Board Room</option>
                        <option value="Vehicle">Vehicle</option>
                    </select>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('request_type', __('Request Type*'), ['class' => 'col-form-label']) }}
                    <select name="request_type" id="request_type" class="form-control select2">
                        <option value="" disabled {{ request()->get('type') == null ? 'selected' : '' }}>Select
                            Request Type</option>
                        <option value="Conference/Board Room"
                            {{ request()->get('type') == 'Conference/Board Room' ? 'selected' : '' }}>Conference/Board
                            Room</option>
                        <option value="Vehicle" {{ request()->get('type') == 'Vehicle' ? 'selected' : '' }}>Vehicle
                        </option>
                    </select>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('purpose', __('Purpose*'), ['class' => 'col-form-label']) }}
                {{ Form::textarea('purpose', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Purpose of use'), 'rows' => '3']) }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('start_date', __('Start Date*'), ['class' => 'col-form-label']) }}
                    {{ Form::text('start_date', null, ['class' => 'form-control d_week current_date', 'autocomplete' => 'off']) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Start Time*</label>
                    <input type="time" name="start_time" class="form-control" style="height:43px;margin-top:10px">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {{ Form::label('end_date', __('End Date*'), ['class' => 'col-form-label']) }}
                    {{ Form::text('end_date', null, ['class' => 'form-control d_week current_date', 'autocomplete' => 'off']) }}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">End Time*</label>
                    <input type="time" name="end_time" class="form-control" style="height:43px;margin-top:10px">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('accompany_by', __('Accompanied By (You can seperate the names with comma)*'), ['class' => 'col-form-label']) }}
                {{ Form::textarea('accompany_by', null, ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('People who are accompanying you'), 'rows' => '3']) }}
            </div>
        </div>
    </div>

    @if (isset($setting['is_enabled']) && $setting['is_enabled'] == 'on')
        <div class="form-group col-md-6">
            {{ Form::label('synchronize_type', __('Synchroniz in Google Calendar ?'), ['class' => 'form-label']) }}
            <div class=" form-switch">
                <input type="checkbox" class="form-check-input mt-2" name="synchronize_type" id="switch-shadow"
                    value="google_calender">
                <label class="form-check-label" for="switch-shadow"></label>
            </div>
        </div>
    @endif
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>
{{ Form::close() }}

<script>
    $(document).ready(function() {
        var now = new Date();
        var month = (now.getMonth() + 1);
        var day = now.getDate();
        if (month < 10) month = "0" + month;
        if (day < 10) day = "0" + day;
        var today = now.getFullYear() + '-' + month + '-' + day;
        $('.current_date').val(today);
    });
</script>

<script>
    $(document).ready(function() {

        setTimeout(() => {
            var employee_id = $('#employee_id').val();

            if (employee_id) {
                $('#employee_id').trigger('change');
            }
        }, 100);
    });
</script>
