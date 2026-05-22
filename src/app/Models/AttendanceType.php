<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceType extends Model
{
    protected $fillable = [
        'name',
        'color',
        'is_paid',
        'is_holiday',
    ];

    protected $casts = [
        'is_paid'    => 'boolean',
        'is_holiday' => 'boolean',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}