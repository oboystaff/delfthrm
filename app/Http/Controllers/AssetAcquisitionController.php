<?php

namespace App\Http\Controllers;

use App\Exports\AssetAcquisitionExport;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\AssetAcquisition;
use App\Models\AssetAcquisitionType;
use Maatwebsite\Excel\Facades\Excel;


class AssetAcquisitionController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Leave')) {
            if (\Auth::user()->type == 'employee') {
                $user     = \Auth::user();
                $employee = Employee::where('user_id', '=', $user->id)->first();
                $assetacquisitions = AssetAcquisition::where('employee_id', '=', $employee->id)->get();
            } else {
                $assetacquisitions = AssetAcquisition::where('created_by', '=', \Auth::user()->creatorId())->with(['employee', 'assetAcquisitionType'])->get();
            }

            return view('assetacquisition.index', compact('assetacquisitions'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('Create Leave')) {

            if (\Auth::user()->type == 'employee') {
                $employees = Employee::where('user_id', '=', \Auth::user()->id)->first();
            } else {
                $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            }

            $assetacquisitiontypes = AssetAcquisitionType::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('assetacquisition.create', compact('employees', 'assetacquisitiontypes'));
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
                    'asset_acquisition_type_id' => 'required',
                    'device_number' => 'required',
                    'name' => 'required',
                    'return_on' => 'required',
                    'reason' => 'required'
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $assetacquisition = new AssetAcquisition();

            $assetacquisition->employee_id = $request->employee_id;
            $assetacquisition->asset_acquisition_type_id = $request->asset_acquisition_type_id;
            $assetacquisition->device_number = $request->device_number;
            $assetacquisition->name = $request->name;
            $assetacquisition->applied_on = date('Y-m-d');
            $assetacquisition->return_on = $request->return_on;
            $assetacquisition->reason = $request->reason;
            $assetacquisition->created_by = $request->user()->creatorId();

            $assetacquisition->save();

            return redirect()->route('assetacquisition.index')->with('success', __('Asset acquisition successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(AssetAcquisition $assetacquisition)
    {
        if (\Auth::user()->can('Create Leave')) {

            if ($assetacquisition->created_by == \Auth::user()->creatorId()) {

                if (\Auth::user()->type == 'employee') {
                    $employees = Employee::where('user_id', '=', \Auth::user()->id)->first();
                } else {
                    $employees = Employee::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('name', 'id');
                }

                $assetacquisitiontypes = AssetAcquisitionType::where('created_by', '=', \Auth::user()->creatorId())->get();

                return view('assetacquisition.edit', compact('assetacquisition', 'employees', 'assetacquisitiontypes'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, AssetAcquisition $assetacquisition)
    {

        if (\Auth::user()->can('Create Leave')) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'employee_id' => 'required',
                    'asset_acquisition_type_id' => 'required',
                    'device_number' => 'required',
                    'name' => 'required',
                    'return_on' => 'required',
                    'reason' => 'required'
                ]
            );

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $assetacquisition->employee_id = $request->employee_id;
            $assetacquisition->asset_acquisition_type_id = $request->asset_acquisition_type_id;
            $assetacquisition->device_number = $request->device_number;
            $assetacquisition->name = $request->name;
            $assetacquisition->applied_on = date('Y-m-d');
            $assetacquisition->return_on = $request->return_on;
            $assetacquisition->reason = $request->reason;
            $assetacquisition->created_by = $request->user()->creatorId();

            $assetacquisition->save();

            return redirect()->route('assetacquisition.index')->with('success', __('Asset acquisition successfully updated.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function action($id)
    {
        $assetacquisition = AssetAcquisition::find($id);
        $employee  = Employee::find($assetacquisition->employee_id);
        $assetacquisitiontype = AssetAcquisitionType::find($assetacquisition->asset_acquisition_type_id);

        return view('assetacquisition.action', compact('employee', 'assetacquisitiontype', 'assetacquisition'));
    }

    public function changeaction(Request $request)
    {
        $assetacquisition = AssetAcquisition::find($request->assetacquisition_id);

        $assetacquisition->status = $request->status;
        $assetacquisition->save();

        return redirect()->route('assetacquisition.index')->with('success', __('Asset acquisition status successfully updated.'));
    }

    public function destroy(AssetAcquisition $assetacquisition)
    {
        if (\Auth::user()->can('Delete Leave')) {
            if ($assetacquisition->created_by == \Auth::user()->creatorId()) {
                $assetacquisition->delete();

                return redirect()->route('assetacquisition.index')->with('success', __('Asset acquisition successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function export()
    {
        $name = 'assset_acquisition_' . date('Y-m-d i:h:s');
        $data = Excel::download(new AssetAcquisitionExport(), $name . '.xlsx');

        return $data;
    }
}
