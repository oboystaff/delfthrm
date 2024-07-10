<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetAcquisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'asset_acquisition_type_id',
        'device_number',
        'name',
        'applied_on',
        'return_on',
        'reason',
        'status',
        'created_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function assetAcquisitionType()
    {
        return $this->belongsTo(AssetAcquisitionType::class, 'asset_acquisition_type_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
