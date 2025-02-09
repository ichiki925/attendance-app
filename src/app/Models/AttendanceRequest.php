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
        'reason',
        'request_status',
        'requested_at',
    ];

    // Attendanceとのリレーション
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    // Userとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
