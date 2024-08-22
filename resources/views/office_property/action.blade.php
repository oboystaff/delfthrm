{{ Form::open(['url' => 'assetacquisition/fireaction', 'method' => 'post']) }}
<div class="modal-body">
    <div class="row">
        <div class="col-12">
            <table class="table modal-table" id="pc-dt-simple">

                <tr role="row">
                    <th>{{ __('Employee') }}</th>
                    <td>{{ !empty($employee->name) ? $employee->name : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Asset Acquisition Type') }}</th>
                    <td>{{ !empty($assetacquisitiontype->name) ? $assetacquisitiontype->name : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Device Number') }}</th>
                    <td>{{ $assetacquisition->device_number }}</td>
                </tr>
                <tr>
                    <th>{{ __('Device Name') }}</th>
                    <td>{{ $assetacquisition->name }}</td>
                </tr>
                <tr>
                    <th>{{ __('Applied Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($assetacquisition->applied_on) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Return Date') }}</th>
                    <td>{{ \Auth::user()->dateFormat($assetacquisition->return_on) }}</td>
                </tr>
                <tr>
                    <th>{{ __('Asset Acquisition Reason') }}</th>
                    <td>{{ !empty($assetacquisition->reason) ? $assetacquisition->reason : '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('Status') }}</th>
                    <td>{{ !empty($assetacquisition->status) ? $assetacquisition->status : '' }}</td>
                </tr>
                <input type="hidden" value="{{ $assetacquisition->id }}" name="assetacquisition_id">
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
