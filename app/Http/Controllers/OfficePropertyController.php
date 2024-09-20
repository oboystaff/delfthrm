<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\OfficeProperty;
use App\Models\Utility;
use Illuminate\Support\Facades\Mail;
use App\Mail\ServiceRequisitionNotification;
use App\Mail\ServiceRequisitionApproval;

class OfficePropertyController extends Controller
{
    public function index(Request $request)
    {
        if (\Auth::user()->can('Manage Leave')) {
            if (\Auth::user()->type == 'employee' || \Auth::user()->type == 'supervisor') {
                $user     = \Auth::user();
                $employee = Employee::where('user_id', '=', $user->id)->first();
                $officeProperties = OfficeProperty::where('employee_id', '=', $employee->id)->get();
            } else {
                $officeProperties = OfficeProperty::where('created_by', '=', \Auth::user()->creatorId())->with(['employee'])->get();
            }

            return view('office_property.index', compact('officeProperties'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create(Request $request)
    {
        if (\Auth::user()->can('Create Leave')) {

            if (\Auth::user()->type == 'employee') {
                $employees = Employee::where('user_id', '=', \Auth::user()->id)->first();
            } else {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            }

            $officeProperties = OfficeProperty::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('office_property.create', compact('employees', 'officeProperties'));
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
                    'purpose' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'start_time' => 'required',
                    'end_time' => 'required',
                    'accompany_by' => 'required',
                    'request_type' => 'required'
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $officeProperty = new OfficeProperty();
            $officeProperty->employee_id = $request->employee_id;
            $officeProperty->request_type = $request->request_type;
            $officeProperty->purpose = $request->purpose;
            $officeProperty->start_date = $request->start_date;
            $officeProperty->end_date = $request->end_date;
            $officeProperty->start_time = $request->start_time;
            $officeProperty->end_time = $request->end_time;
            $officeProperty->accompany_by = $request->accompany_by;
            $officeProperty->created_by = $request->user()->creatorId();

            $officeProperty->save();

            try {
                $settings = Utility::settings();
                $hr = Employee::with('designation')
                    ->whereHas('designation', function ($query) {
                        $query->where('name', 'HR & Office Manager');
                    })
                    ->first();

                $this->mailConfig($settings);
                Mail::to($hr->email)->send(new ServiceRequisitionNotification($officeProperty, $settings));
            } catch (\Exception $ex) {
                //return $ex->getMessage();
            }

            return redirect()->route('office-property.index', ['type' => $request->request_type])->with('success', __('Service requisition request successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(OfficeProperty $officeProperty)
    {
        if (\Auth::user()->can('Create Leave')) {

            if ($officeProperty->created_by == \Auth::user()->creatorId()) {

                if (\Auth::user()->type == 'employee') {
                    $employees = Employee::where('user_id', '=', \Auth::user()->id)->first();
                } else {
                    $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                }

                return view('office_property.edit', compact('officeProperty', 'employees'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, OfficeProperty $officeProperty)
    {
        if (\Auth::user()->can('Create Leave')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required',
                    'purpose' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'accompany_by' => 'required'
                    // 'request_type' => 'required'
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $officeProperty->employee_id = $request->employee_id;
            // $officeProperty->request_type = $request->request_type;
            $officeProperty->purpose = $request->purpose;
            $officeProperty->start_date = $request->start_date;
            $officeProperty->end_date = $request->end_date;
            $officeProperty->start_time = $request->start_time;
            $officeProperty->end_time = $request->end_time;
            $officeProperty->accompany_by = $request->accompany_by;
            $officeProperty->created_by = $request->user()->creatorId();

            $officeProperty->save();

            return redirect()->route('office-property.index')->with('success', __('Service requisition request successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function action($id)
    {
        $officeProperty = OfficeProperty::find($id);
        $employee  = Employee::find($officeProperty->employee_id);

        return view('office_property.action', compact('employee', 'officeProperty'));
    }

    public function changeaction(Request $request)
    {
        $officeProperty = OfficeProperty::find($request->officeproperty_id);

        $officeProperty->status = $request->status;
        $officeProperty->save();

        if (trim($officeProperty->status) == "Approved") {
            try {
                $settings = Utility::settings();

                $this->mailConfig($settings);
                Mail::to($officeProperty->employee->email)->send(new ServiceRequisitionApproval($officeProperty, $settings));
            } catch (\Exception $ex) {
                //return $ex->getMessage();
            }
        }

        return redirect()->route('office-property.index')->with('success', __('Service requisition status successfully updated.'));
    }

    public function destroy(OfficeProperty $officeProperty)
    {
        if (\Auth::user()->can('Delete Leave')) {
            if ($officeProperty->created_by == \Auth::user()->creatorId()) {
                $officeProperty->delete();

                return redirect()->route('office-property.index')->with('success', __('Service requisition request successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
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
