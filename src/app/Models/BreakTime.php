<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakTime extends Model
{
    use HasFactory;
    // テーブル名を明示的に指定
    protected $table = 'breaks';

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
        'break_time',
    ];

    // リレーション: Attendance（多対1）
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
