<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppraisalSetting;

class AppraisalSettingsController extends Controller
{
    public function index()
    {
        if (\Auth::user()->can('Manage Appraisal')) {
            $appraisalsettings = AppraisalSetting::orderBy('created_at', 'DESC')
                ->where('created_by', '=', \Auth::user()->creatorId())
                ->get();

            return view('appraisal_setting.index', compact('appraisalsettings'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if (\Auth::user()->can('Create Appraisal')) {
            return view('appraisal_setting.create');
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if (\Auth::user()->can('Create Appraisal')) {

            $validator = \Validator::make(
                $request->all(),
                [
                    'start_date' => 'required',
                    'end_date' => 'required'
                ]
            );
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $appraisalSetting            = new AppraisalSetting();
            $appraisalSetting->start_date = $request->start_date;
            $appraisalSetting->end_date = $request->end_date;
            $appraisalSetting->created_by = \Auth::user()->creatorId();
            $appraisalSetting->save();

            return redirect()->route('appraisalsetting.index')->with('success', __('Appraisal setting successfully created.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function edit(AppraisalSetting $appraisalsetting)
    {
        if (\Auth::user()->can('Create Appraisal')) {
            return view('appraisal_setting.edit', compact('appraisalsetting'));
        } else {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, AppraisalSetting $appraisalsetting)
    {
        if (\Auth::user()->can('Create Appraisal')) {
            if ($appraisalsetting->created_by == \Auth::user()->creatorId()) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'start_date' => 'required',
                        'end_date' => 'required'
                    ]
                );

                if ($validator->fails()) {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                $appraisalsetting->start_date = $request->start_date;
                $appraisalsetting->end_date = $request->end_date;
                $appraisalsetting->save();

                return redirect()->route('appraisalsetting.index')->with('success', __('Appraisl Setting successfully updated.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy(AppraisalSetting $appraisalsetting)
    {
        if (\Auth::user()->can('Delete Appraisal')) {
            if ($appraisalsetting->created_by == \Auth::user()->creatorId()) {

                $appraisalsetting->delete();

                return redirect()->route('appraisalsetting.index')->with('success', __('Appraisal setting successfully deleted.'));
            } else {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
