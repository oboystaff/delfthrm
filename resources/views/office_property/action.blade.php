{{ Form::open(['url' => 'office-property/fireaction', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table modal-table" id="pc-dt-simple">

                <tr role="row">
                    <th>{{ __('Employee') }}</th>
                    <td>{{ !empty($employee->name) ? $employee->name : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Request Type') }}</th>
                    <td>{{ !empty($officeProperty->request_type) ? $officeProperty->request_type : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Purpose') }}</th>
                    <td>{{ $officeProperty->purpose }}</td>
                </tr>
                <tr>
                    <th>{{ __('Start Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($officeProperty->start_date) }}</td>
                </tr>
                <tr>
                    <th>{{ __('End Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($officeProperty->end_date) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Start Time') }}</th>
                    <td>
                        {{ \Carbon\Carbon::parse($officeProperty->start_time)->setTimezone('Africa/Accra')->format('g:i A') }}
                    </td>
                </tr>
                <tr>
                    <th>{{ __('End Time') }}</th>
                    <td>
                        {{ \Carbon\Carbon::parse($officeProperty->end_time)->setTimezone('Africa/Accra')->format('g:i A') }}
                    </td>
                </tr>
                <tr>
                    <th>{{ __('Accompanied By') }}</th>
                    <td>{{ !empty($officeProperty->accompany_by) ? $officeProperty->accompany_by : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Status') }}</th>
                    <td>{{ !empty($officeProperty->status) ? $officeProperty->status : '' }}</td>
                </tr>
                <input type="hidden" value="{{ $officeProperty->id }}" name="officeproperty_id">
            </table>
        </div>
    </div>
</div>

@if (Auth::user()->type == 'company' || Auth::user()->type == 'hr')
    <div class="modal-footer">
        <input type="submit" value="{{ __('Approved') }}" class="btn btn-success rounded" name="status">
        <input type="submit" value="{{ __('Reject') }}" class="btn btn-danger rounded" name="status">
    </div>
@endif

{{ Form::close() }}
