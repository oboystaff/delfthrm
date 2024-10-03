<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave as LocalLeave;
use App\Models\LeaveType;
use App\Mail\LeaveActionSend;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Imports\EmployeesImport;
use App\Exports\LeaveExport;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\GoogleCalendar\Event as GoogleEvent;
use App\Jobs\Leave\SendLeaveSMS;
use App\Jobs\Releave\SendReleaveSMS;
use App\Models\User;
use App\Mail\LeaveRequestNotification;
use App\Mail\LeaveRequestNotificationMD;

class LeaveController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Leave')) {
            if (\Auth::user()->type == 'employee') {
                $user     = \Auth::user();
                $employee = Employee::where('user_id', '=', $user->id)->first();
                $leaves = LocalLeave::orderBy('created_at', 'DESC')
                    ->where('employee_id', '=', $employee->id)
                    ->get();
            } else if (\Auth::user()->type == 'supervisor') {
                $user     = \Auth::user();
                $employee = Employee::where('user_id', '=', $user->id)->first();
                $leaves = LocalLeave::orderBy('created_at', 'DESC')
                    ->whereHas('employees', function ($query) use ($employee) {
                        $query->where('department_id', '=', $employee->department_id);
                    })
                    ->get();
            } else {
                $leaves = LocalLeave::orderBy('created_at', 'DESC')
                    ->where('created_by', '=', \Auth::user()->creatorId())
                    ->with(['employees', 'leaveType'])
                    ->get();
            }

            return view('leave.index', compact('leaves'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('Create Leave')) {
            if (\Auth::user()->type == 'employee' || \Auth::user()->type == 'supervisor' || \Auth::user()->type == 'hr') {
                $employees = Employee::where('user_id', '=', \Auth::user()->id)->first();
            } else {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            }
            $leavetypess      = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
            $releavers = Employee::where('user_id', '!=', \Auth::user()->id)->get();
            // $leavetypes_days = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
            $leavetypes = [];

            if (\Auth::user()->type == 'employee' || \Auth::user()->type == 'supervisor' || \Auth::user()->type == 'hr') {
                foreach ($leavetypess as $leavetype) {
                    $totalAppliedDays = LocalLeave::where('employee_id', $employees->id)
                        ->where('leave_type_id', $leavetype->id)
                        ->sum('total_leave_days');

                    $leavetypes[$leavetype->id] = $leavetype->title . " ({$totalAppliedDays}/{$leavetype->days})";
                }
            } else {
                $employeess = Employee::where('user_id', '=', \Auth::user()->id)->first();

                foreach ($leavetypess as $leavetype) {
                    $totalAppliedDays = LocalLeave::where('employee_id', $employeess->id)
                        ->where('leave_type_id', $leavetype->id)
                        ->sum('total_leave_days');

                    $leavetypes[$leavetype->id] = $leavetype->title . " ({$totalAppliedDays}/{$leavetype->days})";
                }
            }

            return view('leave.create', compact('employees', 'leavetypes', 'releavers'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Leave')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required',
                    'leave_type_id' => 'required',
                    'employee_releave_id' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'leave_reason' => 'required',
                    'remark' => 'required',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            // $employee = Employee::where('created_by', '=', \Auth::user()->id)->first();
            $leave_type = LeaveType::find($request->leave_type_id);

            $startDate = new \DateTime($request->start_date);
            $endDate = new \DateTime($request->end_date);
            $endDate->add(new \DateInterval('P1D'));
            $total_leave_days = 0;
            $date = Utility::AnnualLeaveCycle();

            if (\Auth::user()->type == 'employee') {
                // Leave day
                $leaves_used   = LocalLeave::where('employee_id', '=', $request->employee_id)
                    ->where('leave_type_id', $leave_type->id)
                    ->where('status', 'Approved')
                    ->whereBetween('created_at', [$date['start_date'], $date['end_date']])
                    ->sum('total_leave_days');

                $leaves_pending  = LocalLeave::where('employee_id', '=', $request->employee_id)
                    ->where('leave_type_id', $leave_type->id)
                    ->where('status', 'Pending')
                    ->whereBetween('created_at', [$date['start_date'], $date['end_date']])
                    ->sum('total_leave_days');
            } else {
                // Leave day
                $leaves_used   = LocalLeave::where('employee_id', '=', $request->employee_id)
                    ->where('leave_type_id', $leave_type->id)
                    ->where('status', 'Approved')
                    ->whereBetween('created_at', [$date['start_date'], $date['end_date']])
                    ->sum('total_leave_days');

                $leaves_pending  = LocalLeave::where('employee_id', '=', $request->employee_id)
                    ->where('leave_type_id', $leave_type->id)
                    ->where('status', 'Pending')
                    ->whereBetween('created_at', [$date['start_date'], $date['end_date']])
                    ->sum('total_leave_days');
            }

            while ($startDate < $endDate) {
                if ($startDate->format('N') < 6) {
                    $total_leave_days++;
                }

                $startDate->add(new \DateInterval('P1D'));
            }

            $return = $leave_type->days - $leaves_used;

            if ($total_leave_days > $return) {
                return redirect()->back()->with('error', __('You are not eligible for leave.'));
            }

            if (!empty($leaves_pending) && $leaves_pending + $total_leave_days > $return) {
                return redirect()->back()->with('error', __('Multiple leave entry is pending.'));
            }

            if ($leave_type->days >= $total_leave_days) {

                $status = 'Pending';
                $leave    = new LocalLeave();
                if (\Auth::user()->type == "employee") {
                    $leave->employee_id = $request->employee_id;
                } else {
                    $leave->employee_id = $request->employee_id;
                }

                if (\Auth::user()->type == "supervisor") {
                    $leave->supervisor_status = 'Approved';
                }

                if (\Auth::user()->type == "hr") {
                    $leave->supervisor_status = 'Approved';
                    $status = 'Approved';
                }

                $leave->leave_type_id    = $request->leave_type_id;
                $leave->employee_releave_id = $request->employee_releave_id;
                $leave->applied_on       = date('Y-m-d');
                $leave->start_date       = $request->start_date;
                $leave->end_date         = $request->end_date;
                $leave->total_leave_days = $total_leave_days;
                $leave->leave_reason     = $request->leave_reason;
                $leave->remark           = $request->remark;
                $leave->status           = $status;
                $leave->created_by       = \Auth::user()->creatorId();
                $leave->save();

                // Google celander
                if ($request->get('synchronize_type')  == 'google_calender') {
                    $type = 'leave';
                    $request1 = new GoogleEvent();
                    $request1->title = !empty(\Auth::user()->getLeaveType($leave->leave_type_id)) ? \Auth::user()->getLeaveType($leave->leave_type_id)->title : '';
                    $request1->start_date = $request->start_date;
                    $request1->end_date = $request->end_date;
                    Utility::addCalendarData($request1, $type);
                }

                //Sedn emeil to HR, Supervisors, MD
                $setings = Utility::settings();
                if ($setings['leave_status'] == 1) {
                    $employee     = Employee::where('id', $leave->employee_id)->where('created_by', '=', \Auth::user()->creatorId())->first();
                    $hr = Employee::with('designation')
                        ->whereHas('designation', function ($query) {
                            $query->where('name', 'HR & Office Manager');
                        })
                        ->first();

                    $uArr = [
                        'leave_email' => $employee->email,
                        'leave_status_name' => $employee->name,
                        'leave_status' => $request->status,
                        'leave_reason' => $leave->leave_reason,
                        'leave_start_date' => $leave->start_date,
                        'leave_end_date' => $leave->end_date,
                        'total_leave_days' => $leave->total_leave_days,
                        'releaver_name' => $leave->releave->name ?? 'N/A'
                    ];

                    //Send email to HR first
                    if (\Auth::user()->type != 'hr') {
                        $settings = Utility::settings();
                        try {
                            $this->mailConfig($settings);
                            Mail::to($hr->email)->send(new LeaveRequestNotification($leave, $settings));
                        } catch (\Exception $ex) {
                            //return $ex->getMessage();
                        }
                    }

                    //Send email to MD if employee is HR
                    if (\Auth::user()->type == 'hr') {
                        $md = Employee::with('designation')
                            ->whereHas('designation', function ($query) {
                                $query->where('name', 'Managing Director');
                            })
                            ->first();

                        $settings = Utility::settings();
                        try {
                            $this->mailConfig($settings);
                            Mail::to($md->email)->send(new LeaveRequestNotificationMD($leave, $settings));
                        } catch (\Exception $ex) {
                            //return $ex->getMessage();
                        }
                    }

                    //Send Reliever email
                    Utility::sendEmailTemplate('releave_status', [$leave->releave->email], $uArr);

                    return redirect()->route('leave.index')->with('success', __('Leave successfully created.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
                }

                return redirect()->route('leave.index')->with('success', __('Leave successfully created.'));
            } else {
                return redirect()->back()->with('error', __('Leave type ' . $leave_type->title . ' is provide maximum ' . $leave_type->days . "  days please make sure your selected days is under " . $leave_type->days . ' days.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(LocalLeave $leave)
    {
        return redirect()->route('leave.index');
    }

    public function edit(LocalLeave $leave)
    {
        if (\Auth::user()->can('Edit Leave')) {
            if ($leave->created_by == \Auth::user()->creatorId()) {

                if (Auth::user()->type == 'employee' || \Auth::user()->type == 'supervisor' || \Auth::user()->type == 'hr') {
                    $employees = Employee::where('employee_id', '=', \Auth::user()->creatorId())->first();
                } else {
                    $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                }

                // $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');

                // $leavetypes = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('title', 'id');
                $leavetypes      = LeaveType::where('created_by', '=', \Auth::user()->creatorId())->get();
                $releavers = Employee::where('user_id', '!=', \Auth::user()->id)->get();

                return view('leave.edit', compact('leave', 'employees', 'leavetypes', 'releavers'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, $leave)
    {
        $leave = LocalLeave::find($leave);
        if (\Auth::user()->can('Edit Leave')) {
            if ($leave->created_by == Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'employee_id' => 'required',
                        'leave_type_id' => 'required',
                        'employee_releave_id' => 'required',
                        'start_date' => 'required',
                        'end_date' => 'required',
                        'leave_reason' => 'required',
                        'remark' => 'required',
                    ]
                );
                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }
                $leave_type = LeaveType::find($request->leave_type_id);
                $employee = Employee::where('employee_id', '=', \Auth::user()->creatorId())->first();

                $startDate = new \DateTime($request->start_date);
                $endDate = new \DateTime($request->end_date);
                $endDate->add(new \DateInterval('P1D'));
                // $total_leave_days = !empty($startDate->diff($endDate)) ? $startDate->diff($endDate)->days : 0;
                $date = Utility::AnnualLeaveCycle();

                if (\Auth::user()->type == 'employee') {
                    // Leave day
                    $leaves_used   = LocalLeave::whereNotIn('id', [$leave->id])->where('employee_id', '=', $employee->id)->where('leave_type_id', $leave_type->id)->where('status', 'Approved')->whereBetween('created_at', [$date['start_date'], $date['end_date']])->sum('total_leave_days');

                    $leaves_pending  = LocalLeave::whereNotIn('id', [$leave->id])->where('employee_id', '=', $employee->id)->where('leave_type_id', $leave_type->id)->where('status', 'Pending')->whereBetween('created_at', [$date['start_date'], $date['end_date']])->sum('total_leave_days');
                } else {
                    // Leave day
                    $leaves_used   = LocalLeave::whereNotIn('id', [$leave->id])->where('employee_id', '=', $request->employee_id)->where('leave_type_id', $leave_type->id)->where('status', 'Approved')->whereBetween('created_at', [$date['start_date'], $date['end_date']])->sum('total_leave_days');

                    $leaves_pending  = LocalLeave::whereNotIn('id', [$leave->id])->where('employee_id', '=', $request->employee_id)->where('leave_type_id', $leave_type->id)->where('status', 'Pending')->whereBetween('created_at', [$date['start_date'], $date['end_date']])->sum('total_leave_days');
                }

                $total_leave_days = !empty($startDate->diff($endDate)) ? $startDate->diff($endDate)->days : 0;

                $return = $leave_type->days - $leaves_used;
                if ($total_leave_days > $return) {
                    return redirect()->back()->with('error', __('You are not eligible for leave.'));
                }

                if (!empty($leaves_pending) && $leaves_pending + $total_leave_days > $return) {
                    return redirect()->back()->with('error', __('Multiple leave entry is pending.'));
                }

                if ($leave_type->days >= $total_leave_days) {
                    if (\Auth::user()->type == 'employee') {
                        $leave->employee_id = $employee->id;
                    } else {
                        $leave->employee_id      = $request->employee_id;
                    }
                    $leave->leave_type_id    = $request->leave_type_id;
                    $leave->employee_releave_id = $request->employee_releave_id;
                    $leave->start_date       = $request->start_date;
                    $leave->end_date         = $request->end_date;
                    $leave->total_leave_days = $total_leave_days;
                    $leave->leave_reason     = $request->leave_reason;
                    $leave->remark           = $request->remark;
                    // $leave->status           = $request->status;

                    $leave->save();

                    return redirect()->route('leave.index')->with('success', __('Leave successfully updated.'));
                } else {
                    return redirect()->back()->with('error', __('Leave type ' . $leave_type->name . ' is provide maximum ' . $leave_type->days . "  days please make sure your selected days is under " . $leave_type->days . ' days.'));
                }
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(LocalLeave $leave)
    {
        if (\Auth::user()->can('Delete Leave')) {
            if ($leave->created_by == \Auth::user()->creatorId()) {
                $leave->delete();

                return redirect()->route('leave.index')->with('success', __('Leave successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function export()
    {
        $name = 'leave_' . date('Y-m-d i:h:s');
        $data = Excel::download(new LeaveExport(), $name . '.xlsx');

        return $data;
    }

    public function action($id)
    {
        $leave     = LocalLeave::find($id);
        $employee  = Employee::find($leave->employee_id);
        $leavetype = LeaveType::find($leave->leave_type_id);

        return view('leave.action', compact('employee', 'leavetype', 'leave'));
    }

    public function changeaction(Request $request)
    {
        $leave = LocalLeave::find($request->leave_id);
        $md = Employee::with('designation')
            ->whereHas('designation', function ($query) {
                $query->where('name', 'Managing Director');
            })
            ->first();

        if (\Auth::user()->type == 'supervisor') {
            $leave->supervisor_status = $request->status;

            $leave->save();
        } else if (\Auth::user()->type == 'hr') {
            $leave->status = $request->status;

            $leave->save();

            $settings = Utility::settings();
            try {
                $this->mailConfig($settings);
                Mail::to($md->email)->send(new LeaveRequestNotificationMD($leave, $settings));
            } catch (\Exception $ex) {
                //return $ex->getMessage();
            }
        } else {
            $leave->md_status = $request->status;

            if ($leave->md_status == 'Approved') {
                $startDate               = new \DateTime($leave->start_date);
                $endDate                 = new \DateTime($leave->end_date);
                $endDate->add(new \DateInterval('P1D'));
                // $total_leave_days        = $startDate->diff($endDate)->days;
                $total_leave_days        = !empty($startDate->diff($endDate)) ? $startDate->diff($endDate)->days : 0;
                $leave->total_leave_days = $total_leave_days;
                $leave->md_status           = 'Approved';
            }

            $leave->save();

            // Leave SMS settings (twilio)
            // $setting = Utility::settings(\Auth::user()->creatorId());
            // $emp = Employee::find($leave->employee_id);
            // if (isset($setting['twilio_leave_approve_notification']) && $setting['twilio_leave_approve_notification'] == 1) {
            //     // $msg = __("Your leave has been") . ' ' . $leave->status . '.';

            //     $uArr = [
            //         'leave_status' => $leave->status,
            //     ];

            //     Utility::send_twilio_msg($emp->phone, 'leave_approve_reject', $uArr);
            // }

            //Leave SMS settings (MNotifier)
            dispatch(new SendLeaveSMS($leave));
            dispatch(new SendReleaveSMS($leave));

            //Leave email settings
            $setings = Utility::settings();
            if ($setings['leave_status'] == 1) {
                $employee     = Employee::where('id', $leave->employee_id)->where('created_by', '=', \Auth::user()->creatorId())->first();

                $uArr = [
                    'leave_email' => $employee->email,
                    'leave_status_name' => $employee->name,
                    'leave_status' => $request->status,
                    'leave_reason' => $leave->leave_reason,
                    'leave_start_date' => $leave->start_date,
                    'leave_end_date' => $leave->end_date,
                    'total_leave_days' => $leave->total_leave_days,
                ];

                $resp = Utility::sendEmailTemplate('leave_status', [$employee->email], $uArr);
                return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
            }
        }

        return redirect()->route('leave.index')->with('success', __('Leave status successfully updated.'));
    }

    public function jsoncount(Request $request)
    {
        $date = Utility::AnnualLeaveCycle();
        $leave_counts = LeaveType::select(\DB::raw('COALESCE(SUM(leaves.total_leave_days),0) AS total_leave, leave_types.title, leave_types.days,leave_types.id'))
            ->leftjoin(
                'leaves',
                function ($join) use ($request, $date) {
                    $join->on('leaves.leave_type_id', '=', 'leave_types.id');
                    $join->where('leaves.employee_id', '=', $request->employee_id);
                    $join->where('leaves.status', '=', 'Approved');
                    $join->whereBetween('leaves.created_at', [$date['start_date'], $date['end_date']]);
                }
            )->where('leave_types.created_by', '=', \Auth::user()->creatorId())->groupBy('leave_types.id')->get();
        return $leave_counts;
    }

    public function calender(Request $request)
    {
        $created_by = \Auth::user()->creatorId();
        $Meetings = LocalLeave::where('created_by', $created_by)->get();

        $today_date = date('m');
        $current_month_event = LocalLeave::select('id', 'start_date', 'employee_id', 'created_at')->whereRaw('MONTH(start_date)=' . $today_date)->get();

        $arrMeeting = [];

        foreach ($Meetings as $meeting) {
            $arr['id']        = $meeting['id'];
            $arr['employee_id']     = $meeting['employee_id'];
            // $arr['leave_type_id']     = date('Y-m-d', strtotime($meeting['start_date']));
        }

        $leaves = LocalLeave::where('created_by', '=', \Auth::user()->creatorId())->get();
        if (\Auth::user()->type == 'employee') {
            $user     = \Auth::user();
            $employee = Employee::where('user_id', '=', $user->id)->first();
            $leaves   = LocalLeave::where('employee_id', '=', $employee->id)->get();
        } else {
            $leaves = LocalLeave::where('created_by', '=', \Auth::user()->creatorId())->get();
        }

        return view('leave.calender', compact('leaves'));
    }

    public function get_leave_data(Request $request)
    {
        $arrayJson = [];
        if ($request->get('calender_type') == 'google_calender') {
            $type = 'leave';
            $arrayJson =  Utility::getCalendarData($type);
        } else {
            $data = LocalLeave::where('created_by', \Auth::user()->creatorId())->get();

            foreach ($data as $val) {
                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => !empty(\Auth::user()->getLeaveType($val->leave_type_id)) ? \Auth::user()->getLeaveType($val->leave_type_id)->title : '',
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => $val->color,
                    "textColor" => '#FFF',
                    "allDay" => true,
                    "url" => route('leave.action', $val['id']),
                ];
            }
        }

        return $arrayJson;
    }

    public function mailConfig($settings)
    {
        config(
            [
                'mail.driver' => $settings['mail_driver'] ? $settings['mail_driver'] : '',
                'mail.host' => $settings['mail_host'] ? $settings['mail_host'] : '',
                'mail.port' => $settings['mail_port'] ? $settings['mail_port'] : '',
                'mail.encryption' => $settings['mail_encryption'] ? $settings['mail_encryption'] : '',
                'mail.username' => $settings['mail_username'] ? $settings['mail_username'] : '',
                'mail.password' => $settings['mail_password'] ? $settings['mail_password'] : '',
                'mail.from.address' => $settings['mail_from_address'] ? $settings['mail_from_address'] : '',
                'mail.from.name' => $settings['mail_from_name'] ? $settings['mail_from_name'] : '',
            ]
        );
    }
}
