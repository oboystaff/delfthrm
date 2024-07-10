<?php

namespace App\Exports;

use App\Models\AssetAcquisition;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Employee;


class AssetAcquisitionExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $user     = \Auth::user();
        $data = AssetAcquisition::where('created_by', \Auth::user()->creatorId())->get();

        if (\Auth::user()->type == 'employee') {
            $employee = Employee::where('user_id', '=', $user->id)->first();

            $data = AssetAcquisition::where('employee_id', '=', $employee->id)->where('created_by', \Auth::user()->creatorId())->get();

            foreach ($data as $k => $assetacquisition) {
                $data[$k]["employee_id"] = Employee::employee_name($assetacquisition->employee_id);
                $data[$k]["asset_acquisition_type_id"] = !empty(\Auth::user()->getAssetAcquisitionType($assetacquisition->asset_acquisition_type_id)) ? \Auth::user()->getAssetAcquisitionType($assetacquisition->asset_acquisition_type_id)->name : '';
                $data[$k]["created_by"] = Employee::login_user($assetacquisition->created_by);
                unset($assetacquisition->created_at, $assetacquisition->updated_at);
            }
        } else {
            $data = AssetAcquisition::where('created_by', \Auth::user()->creatorId())->get();
            foreach ($data as $k => $assetacquisition) {
                $data[$k]["employee_id"] = Employee::employee_name($assetacquisition->employee_id);
                $data[$k]["asset_acquisition_type_id"] = !empty(\Auth::user()->getAssetAcquisitionType($assetacquisition->asset_acquisition_type_id)) ? \Auth::user()->getAssetAcquisitionType($assetacquisition->asset_acquisition_type_id)->name : '';
                $data[$k]["created_by"] = Employee::login_user($assetacquisition->created_by);
                unset($assetacquisition->created_at, $assetacquisition->updated_at);
            }

            return $data;
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            "ID",
            "Employee Name",
            "Asset Acquisition Type ",
            "Device Number",
            "Device Name",
            "Applied Date",
            "Return Date",
            "Reason",
            "Status",
            "Created By"
        ];
    }
}
