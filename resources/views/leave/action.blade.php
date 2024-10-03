{{ Form::open(['url' => 'leave/changeaction', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table modal-table" id="pc-dt-simple">

                <tr role="row">
                    <th>{{ __('Employee') }}</th>
                    <td>{{ !empty($employee->name) ? $employee->name : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Leave Type ') }}</th>
                    <td>{{ !empty($leavetype->title) ? $leavetype->title : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Releave Officer') }}</th>
                    <td>{{ $leave->releave->name ?? '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Appplied On') }}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->applied_on) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Start Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->start_date) }}</td>
                </tr>
                <tr>
                    <th>{{ __('End Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($leave->end_date) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Leave Reason') }}</th>
                    <td>{{ !empty($leave->leave_reason) ? $leave->leave_reason : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Status') }}</th>
                    <td>{{ !empty($leave->status) ? $leave->status : '' }}</td>
                </tr>
                <input type="hidden" value="{{ $leave->id }}" name="leave_id">
            </table>
        </div>
    </div>
</div>

@if (Auth::user()->type == 'company' || Auth::user()->type == 'hr')
    @if ($leave->supervisor_status == 'Pending')
        <p style="margin-left:30px;color:green;"><span style="font-weight:bold">Note:</span> Waiting for approval from
            the employee's supervisor on the leave request.
        </p>
    @elseif ($leave->supervisor_status == 'Reject')
        <p style="margin-left:30px;color:red;"><span style="font-weight:bold">Note:</span> Employee leave request has
            been rejected by their supervisor</p>
    @else
        <div class="modal-footer">
            <input type="submit" value="{{ __('Approve') }}" class="btn btn-success rounded" name="status">
            <input type="submit" value="{{ __('Reject') }}" class="btn btn-danger rounded" name="status">
        </div>
    @endif
@elseif (Auth::user()->type == 'md')
    @if ($leave->supervisor_status == 'Pending')
        <p style="margin-left:30px;color:green;"><span style="font-weight:bold">Note:</span> Waiting for approval from
            the employee's supervisor on the leave request.
        </p>
    @elseif ($leave->supervisor_status == 'Reject')
        <p style="margin-left:30px;color:red;"><span style="font-weight:bold">Note:</span> Employee leave request has
            been rejected by their supervisor</p>
    @elseif ($leave->status == 'Pending')
        <p style="margin-left:30px;color:green;"><span style="font-weight:bold">Note:</span> Waiting for approval from
            the HR on the employee leave request.
        </p>
    @elseif($leave->status == 'Reject')
        <p style="margin-left:30px;color:red;"><span style="font-weight:bold">Note:</span> Employee leave request has
            been rejected by the HR</p>
    @else
        <div class="modal-footer">
            <input type="submit" value="{{ __('Approve') }}" class="btn btn-success rounded" name="status">
            <input type="submit" value="{{ __('Reject') }}" class="btn btn-danger rounded" name="status">
        </div>
    @endif
@elseif (Auth::user()->type == 'supervisor')
    <div class="modal-footer">
        <input type="submit" value="{{ __('Approve') }}" class="btn btn-success rounded" name="status">
        <input type="submit" value="{{ __('Reject') }}" class="btn btn-danger rounded" name="status">
    </div>
@endif

{{ Form::close() }}
