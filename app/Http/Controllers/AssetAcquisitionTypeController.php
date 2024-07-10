<?php

namespace App\Http\Controllers;

use App\Models\AssetAcquisitionType;
use Illuminate\Http\Request;

class AssetAcquisitionTypeController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Payslip Type')) {
            $assetacquisitiontypes = AssetAcquisitionType::where('created_by', '=', \Auth::user()->creatorId())->get();

            return view('assetacquisitiontype.index', compact('assetacquisitiontypes'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('Create Payslip Type')) {
            return view('assetacquisitiontype.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Payslip Type')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'name' => 'required|max:20',
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $assetacquisitiontype             = new AssetAcquisitionType();
            $assetacquisitiontype->name       = $request->name;
            $assetacquisitiontype->created_by = \Auth::user()->creatorId();
            $assetacquisitiontype->save();

            return redirect()->route('assetacquisitiontype.index')->with('success', __('Asset Acquisition Type successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(AssetAcquisitionType $assetacquisitiontype)
    {
        if (\Auth::user()->can('Edit Payslip Type')) {
            if ($assetacquisitiontype->created_by == \Auth::user()->creatorId()) {

                return view('assetacquisitiontype.edit', compact('assetacquisitiontype'));
            } else {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, AssetAcquisitionType $assetacquisitiontype)
    {
        if (\Auth::user()->can('Edit Payslip Type')) {
            if ($assetacquisitiontype->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'name' => 'required|max:20',
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $assetacquisitiontype->name = $request->name;
                $assetacquisitiontype->save();

                return redirect()->route('assetacquisitiontype.index')->with('success', __('Asset Acquisition Type successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(AssetAcquisitionType $assetacquisitiontype)
    {
        if (\Auth::user()->can('Delete Payslip Type')) {
            if ($assetacquisitiontype->created_by == \Auth::user()->creatorId()) {

                $assetacquisitiontype->delete();

                return redirect()->route('assetacquisitiontype.index')->with('success', __('Asset acquisition type successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
