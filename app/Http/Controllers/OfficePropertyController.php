<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\OfficeProperty;
use Google\Service\CivicInfo\Office;

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
                    'end_time' => 'reqyured',
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

            return redirect()->route('office-property.index', ['type' => $request->request_type])->with('success', __('Office property request successfully created.'));
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

            return redirect()->route('office-property.index')->with('success', __('Office property request successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function action($id)
    {
        $officeProperty = OfficeProperty::find($id);
        $employee  = Employee::find($officeProperty->employee_id);

        return view('office_roperty.action', compact('employee', 'officeProperty'));
    }

    public function changeaction(Request $request)
    {
        $assetacquisition = AssetAcquisition::find($request->assetacquisition_id);

        $assetacquisition->status = $request->status;
        $assetacquisition->save();

        return redirect()->route('assetacquisition.index')->with('success', __('Asset acquisition status successfully updated.'));
    }

    public function destroy(OfficeProperty $officeProperty)
    {
        if (\Auth::user()->can('Delete Leave')) {
            if ($officeProperty->created_by == \Auth::user()->creatorId()) {
                $officeProperty->delete();

                return redirect()->route('office-property.index')->with('success', __('Office property request successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
