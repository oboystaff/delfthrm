@php
    $plan = Utility::getChatGPTSettings();
@endphp

{{ Form::model($officeProperty, ['route' => ['office-property.update', $officeProperty->id], 'method' => 'PUT']) }}
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
                    {{ Form::select('employee_id', $employees, null, ['class' => 'form-control select2', 'placeholder' => __('Select Employee')]) }}
                </div>
            </div>
        </div>
    @else
        {!! Form::hidden('employee_id', !empty($employees) ? $employees->id : 0, ['id' => 'employee_id']) !!}
    @endif

    <input type="hidden" name="request_type" id="request_type" value="{{ request()->type }}">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {{ Form::label('purpose', __('Purpose'), ['class' => 'col-form-label']) }}
                {{ Form::textarea('purpose', null, ['class' => 'form-control', 'placeholder' => __('Purpose of use'), 'rows' => '3']) }}
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
                    <input type="time" name="start_time" class="form-control" style="height:43px;margin-top:10px"
                        value="{{ $officeProperty->start_time }}">
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
                    <input type="time" name="end_time" class="form-control" style="height:43px;margin-top:10px"
                        value="{{ $officeProperty->end_time }}">
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

    @role('Company')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('status', __('Status'), ['class' => 'col-form-label']) }}
                    <select name="status" id="" class="form-control select2">
                        <option value="">{{ __('Select Status') }}</option>
                        <option value="Pending" @if ($officeProperty->status == 'Pending') selected="" @endif>{{ __('Pending') }}
                        </option>
                        <option value="Approved" @if ($officeProperty->status == 'Approved') selected="" @endif>{{ __('Approved') }}
                        </option>
                        <option value="Reject" @if ($officeProperty->status == 'Reject') selected="" @endif>{{ __('Reject') }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    @endrole
</div>
<div class="modal-footer">
    <button type="button" class="btn  btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
    <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">

</div>
{{ Form::close() }}

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
