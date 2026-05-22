<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\AttendanceRequest;
use App\Models\BreakTime;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;


    protected $table = 'attendances';


    protected $fillable = [
        'user_id',
        'attendance_type_id',
        'date',
        'start_time',
        'end_time',
        'total_time',
        'work_minutes',
        'overtime_minutes',
        'late_night_minutes',
        'status',
        'remarks',
    ];


    public function setEndTimeAttribute($value)
    {
        $this->attributes['end_time'] = ($value === '-' || empty($value)) ? null : $value;
    }


    public function getDateAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }

    public function attendanceType()
    {
        return $this->belongsTo(AttendanceType::class);
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class, 'attendance_id');
    }

    public function breaks()
    {
        return $this->hasMany(BreakTime::class, 'attendance_id', 'id');
    }

    public function calculateTimes()
    {
        if (!$this->start_time || !$this->end_time) {
            return;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        // 総労働時間（秒）
        $totalSeconds = $end->diffInSeconds($start);

        // 休憩時間（秒）
        $breakSeconds = $this->breaks->sum(function ($break) {
            if (!$break->break_time) return 0;
            [$hours, $minutes] = explode(':', $break->break_time);
            return ($hours * 3600) + ($minutes * 60);
        });

        // 実労働時間（秒）
        $workSeconds = max($totalSeconds - $breakSeconds, 0);
        $workMinutes = (int) ceil($workSeconds / 60);

        // 丸め処理を適用
        try {
            $workMinutes = \App\Models\RoundingSetting::applyRounding($workMinutes);
        } catch (\Exception $e) {
            // 丸め設定が取得できない場合はそのまま
        }

        // 残業時間（8時間超の分）
        $regularMinutes = 8 * 60;
        $overtimeMinutes = max($workMinutes - $regularMinutes, 0);

        // 深夜時間（22:00〜5:00）
        $lateNightMinutes = $this->calcLateNightMinutes($start, $end, $breakSeconds);

        // total_timeを文字列で保存
        $hours = floor($workMinutes / 60);
        $minutes = $workMinutes % 60;

        $this->work_minutes = $workMinutes;
        $this->overtime_minutes = $overtimeMinutes;
        $this->late_night_minutes = $lateNightMinutes;
        $this->total_time = sprintf('%02d:%02d', $hours, $minutes);
    }

    private function calcLateNightMinutes(Carbon $start, Carbon $end, int $breakSeconds): int
    {
        $date = $start->format('Y-m-d');

        // 深夜帯の開始・終了
        $lateStart = Carbon::parse($date . ' 22:00:00');
        $lateEnd = Carbon::parse($date . ' 05:00:00')->addDay();

        // 勤務時間と深夜帯の重複を計算
        $overlapStart = $start->max($lateStart);
        $overlapEnd = $end->min($lateEnd);

        if ($overlapStart >= $overlapEnd) {
            return 0;
        }

        $lateSeconds = $overlapEnd->diffInSeconds($overlapStart);

        // 深夜帯の休憩は按分して引く（簡易計算）
        $totalSeconds = max($end->diffInSeconds($start), 1);
        $lateBreakSeconds = (int) ($breakSeconds * ($lateSeconds / $totalSeconds));

        $lateNightSeconds = max($lateSeconds - $lateBreakSeconds, 0);

        return (int) ceil($lateNightSeconds / 60);
    }

    public function getOvertimeFormatted(): string
    {
        $minutes = $this->overtime_minutes ?? 0;
        return sprintf('%d:%02d', floor($minutes / 60), $minutes % 60);
    }

    /**
     * 表示用: 深夜時間を H:MM 形式で返す
     */
    public function getLateNightFormatted(): string
    {
        $minutes = $this->late_night_minutes ?? 0;
        return sprintf('%d:%02d', floor($minutes / 60), $minutes % 60);
    }

}
