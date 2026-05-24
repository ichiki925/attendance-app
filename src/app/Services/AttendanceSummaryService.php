<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClosingDaySetting;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSummaryService
{
    /**
     * 締め日ベースの集計期間を返す
     * @return array ['start' => Carbon, 'end' => Carbon, 'label' => string]
     */
    public function getCurrentPeriod(string $periodParam = null): array
    {
        $closing = ClosingDaySetting::getActive();
        $closingDay = $closing->closing_day;

        $base = $periodParam
            ? Carbon::parse($periodParam)
            : Carbon::now();

        if ($closingDay === 31) {
            $start = $base->copy()->startOfMonth();
            $end   = $base->copy()->endOfMonth();
            $label = $base->format('Y/m') . '（末日締め）';
        } else {
            $start = $base->copy()->subMonth()->day($closingDay)->addDay()->startOfDay();
            $end   = $base->copy()->day($closingDay)->endOfDay();
            $label = $start->format('Y/m/d') . ' 〜 ' . $end->format('Y/m/d');
        }

        return [
            'start' => $start,
            'end'   => $end,
            'label' => $label,
            'prev'  => $base->copy()->subMonth()->format('Y-m'),
            'next'  => $base->copy()->addMonth()->format('Y-m'),
        ];
    }

    /**
     * 期間内の全スタッフ集計を返す
     */
    public function summarizeAllStaff(Carbon $start, Carbon $end): array
    {
        $staffList = User::where('role', '!=', 'admin')->get();
        $results = [];

        foreach ($staffList as $staff) {
            $attendances = Attendance::where('user_id', $staff->id)
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->with('breaks', 'attendanceType')
                ->get()
                ->each(fn($a) => $a->calculateTimes());

            $totalWorkMinutes     = $attendances->sum('work_minutes');
            $totalOvertimeMinutes = $attendances->sum('overtime_minutes');

            // 法定休日出勤日数（attendance_typeのis_holiday=trueかつ実際に出勤しているもの）
            $holidayWorkDays = $attendances->filter(function ($a) {
                return $a->attendanceType && $a->attendanceType->is_holiday
                    && $a->start_time && $a->end_time;
            })->count();

            $results[] = [
                'staff'                 => $staff,
                'total_work_minutes'    => $totalWorkMinutes,
                'total_overtime_minutes'=> $totalOvertimeMinutes,
                'holiday_work_days'     => $holidayWorkDays,
                'is_overtime_alert'     => $totalOvertimeMinutes >= (60 * 60), // 60h超
                'is_holiday_alert'      => $holidayWorkDays > 0,
            ];
        }

        return $results;
    }

    public static function formatMinutes(int $minutes): string
    {
        return sprintf('%d:%02d', floor($minutes / 60), $minutes % 60);
    }
}