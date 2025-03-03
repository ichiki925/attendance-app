<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    // break_start をセットするときに '-' や '' を NULL に変換
    public function setBreakStartAttribute($value)
    {
        $this->attributes['break_start'] = !empty($value) ? $value : null;
    }

    public function getBreakStartAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }

    public function getBreakEndAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : null;
    }


    // リレーション: Attendance（多対1）
    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }
}
