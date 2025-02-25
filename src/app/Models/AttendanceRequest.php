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
        'created_at' => 'datetime', // 追加
        'updated_at' => 'datetime', // 追加
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

    public function breaks()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id');
    }
}
