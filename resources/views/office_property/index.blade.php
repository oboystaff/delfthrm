@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Office Property Request') }}
@endsection


@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Office Property Request') }}</li>
@endsection

@section('action-button')
    @can('Create Leave')
        <a href="javascript:void(0);" data-url="{{ route('office-property.create', ['type' => request()->get('type')]) }}"
            data-ajax-popup="true" data-title="{{ __('Create New Office Property Request') }}" data-size="lg"
            data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary" data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection

@section('content')
    <div class="row">

        <div class="col-xl-12">
            <div class="card">
                <div class="card-header card-body table-border-style">
                    {{-- <h5> </h5> --}}
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    @if (\Auth::user()->type != 'employee')
                                        <th>{{ __('Employee') }}</th>
                                    @endif
                                    <th>{{ __('Purpose') }}</th>
                                    <th>{{ __('Request Type') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Accompany By') }}</th>
                                    {{-- <th>{{ __('Status') }}</th> --}}
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($officeProperties as $officeProperty)
                                    <tr>
                                        @if (\Auth::user()->type != 'employee')
                                            <td>{{ $officeProperty->employee->name ?? '' }}
                                            </td>
                                        @endif
                                        <td>{{ $officeProperty->purpose }}</td>
                                        <td>{{ $officeProperty->request_type ?? 'N/A' }}</td>
                                        <td>{{ \Auth::user()->dateFormat($officeProperty->start_date) }}</td>
                                        <td>{{ \Auth::user()->dateFormat($officeProperty->end_date) }}</td>
                                        <td>{{ $officeProperty->accompany_by ?? '' }}</td>
                                        {{-- <td>
                                            @if ($officeProperty->status == 'Pending')
                                                <div class="badge bg-warning p-2 px-3 rounded status-badge5">
                                                    {{ $officeProperty->status }}</div>
                                            @elseif($officeProperty->status == 'Approved')
                                                <div class="badge bg-success p-2 px-3 rounded status-badge5">
                                                    {{ $officeProperty->status }}</div>
                                            @elseif($officeProperty->status == 'Reject')
                                                <div class="badge bg-danger p-2 px-3 rounded status-badge5">
                                                    {{ $officeProperty->status }}</div>
                                            @endif
                                        </td> --}}

                                        <td class="Action">

                                            <span>
                                                @if (\Auth::user()->type != 'employee')
                                                    @can('Edit Leave')
                                                        <div class="action-btn bg-info ms-2">
                                                            <a href="javascript:void(0);"
                                                                class="mx-3 btn btn-sm  align-items-center" data-size="lg"
                                                                data-url="{{ URL::to('office-property/' . $officeProperty->id . '/edit') }}"
                                                                data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                                title="" data-title="{{ __('Edit Office Property') }}"
                                                                data-bs-original-title="{{ __('Edit') }}">
                                                                <i class="ti ti-pencil text-white"></i>
                                                            </a>
                                                        </div>
                                                    @endcan
                                                    @can('Delete Leave')
                                                        @if (\Auth::user()->type != 'employee')
                                                            <div class="action-btn bg-danger ms-2">
                                                                {!! Form::open([
                                                                    'method' => 'DELETE',
                                                                    'route' => ['office-property.destroy', $officeProperty->id],
                                                                    'id' => 'delete-form-' . $officeProperty->id,
                                                                ]) !!}
                                                                <a href="javascript:void(0);"
                                                                    class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                                                    data-bs-toggle="tooltip" title=""
                                                                    data-bs-original-title="Delete" aria-label="Delete"><i
                                                                        class="ti ti-trash text-white text-white"></i></a>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    @endcan
                                                @else
                                                    <div class="action-btn bg-success ms-2">
                                                        <a href="javascript:void(0);"
                                                            class="mx-3 btn btn-sm  align-items-center" data-size="lg"
                                                            data-url="{{ URL::to('office-property/' . $officeProperty->id . '/action') }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title="" data-title="{{ __('Office Property Action') }}"
                                                            data-bs-original-title="{{ __('Manage Office Property') }}">
                                                            <i class="ti ti-caret-right text-white"></i>
                                                        </a>
                                                    </div>
                                                @endif

                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('script-page')
    <script>
        $(document).on('change', '#employee_id', function() {
            var employee_id = $(this).val();

            $.ajax({
                url: '{{ route('leave.jsoncount') }}',
                type: 'POST',
                data: {
                    "employee_id": employee_id,
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    var oldval = $('#leave_type_id').val();
                    $('#leave_type_id').empty();
                    $('#leave_type_id').append(
                        '<option value="">{{ __('Select Leave Type') }}</option>');

                    $.each(data, function(key, value) {

                        if (value.total_leave == value.days) {
                            $('#leave_type_id').append('<option value="' + value.id +
                                '" disabled>' + value.title + '&nbsp(' + value.total_leave +
                                '/' + value.days + ')</option>');
                        } else {
                            $('#leave_type_id').append('<option value="' + value.id + '">' +
                                value.title + '&nbsp(' + value.total_leave + '/' + value
                                .days + ')</option>');
                        }
                        if (oldval) {
                            if (oldval == value.id) {
                                $("#leave_type_id option[value=" + oldval + "]").attr(
                                    "selected", "selected");
                            }
                        }
                    });
                }
            });
        });
    </script>
@endpush
