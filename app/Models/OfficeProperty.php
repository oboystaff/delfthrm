<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfficeProperty extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'request_type',
        'purpose',
        'start_date',
        'end_date',
        'accompany_by',
        'status',
        'created_by'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
