<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'user_id',
        'start_time',
        'end_time',
        'reason',
        'request_status',
    ];

    protected $casts = [
        'start_time' => 'string',
        'end_time' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
