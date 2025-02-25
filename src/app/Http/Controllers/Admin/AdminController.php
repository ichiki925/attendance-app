<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use App\Models\AttendanceRequest as AttendanceRequestModel;
use App\Http\Requests\AttendanceRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class AdminController extends Controller
{
    public function attendanceIndex(Request $request)
    {
        $selectedDate = $request->query('date', now()->toDateString());

        // 指定された日付の勤怠データを取得
        $attendances = Attendance::where('date', $selectedDate)
            ->whereHas('user', function ($query) {
                $query->where('role', '!=', 'admin'); // 管理者を除外
            })
            ->with('user', 'breaks')
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($attendance) {
            // 休憩時間の合計を計算
            $totalBreakSeconds = $attendance->breaks->sum(function ($break) {
                return strtotime($break->break_time) - strtotime('00:00:00');
            });

            // 勤務時間を計算（休憩時間を引く）
            if ($attendance->start_time && $attendance->end_time) {
                $workDurationSeconds = strtotime($attendance->end_time) - strtotime($attendance->start_time);
                $netWorkSeconds = max($workDurationSeconds - $totalBreakSeconds, 0);

                $attendance->total_time = gmdate('H:i:s', $netWorkSeconds);
            } else {
                $attendance->total_time = '-';
            }

            $attendance->total_break_time = gmdate('H:i:s', $totalBreakSeconds);

            return $attendance;
        });


        return view('admin.attendance_list', compact('attendances', 'selectedDate'));
    }

    public function showAttendanceDetail($id)
    {
        $attendance = Attendance::with(['user', 'breaks'])->findOrFail($id);
        return view('admin.attendance_detail', compact('attendance'));
    }


    public function updateAttendance(AttendanceRequest $request, $id)
    {
        $validated = $request->validated();

        // 該当の勤怠データを取得
        $attendance = Attendance::with('breaks')->findOrFail($id);

        // 勤務時間 & 備考の更新
        $attendance->start_time = $validated['start_time'];
        $attendance->end_time = $validated['end_time'] ?? null;
        $attendance->remarks = $validated['remarks'] ?? null;
        $attendance->save();

        // 既存の休憩データを削除（新しいデータで上書き）
        $attendance->breaks()->delete();

        // 新しい休憩データを保存
        if (!empty($validated['breaks'])) {
            foreach ($validated['breaks'] as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $break['start'],
                        'break_end' => $break['end'],
                        'break_time' => gmdate('H:i:s', strtotime($break['end']) - strtotime($break['start'])), // 休憩時間を計算
                    ]);
                }
            }
        }

        return redirect()->route('admin.attendance.detail', $id)
                        ->with('success', '勤怠情報を更新しました。');
    }


    public function staffIndex()
    {
        $staffs = User::where('role', '!=', 'admin')->get();
        return view('admin.staff_list', compact('staffs'));
    }

    public function staffAttendanceIndex(Request $request, $id)
    {
        $staff = User::where('role', '!=', 'admin')->findOrFail($id);

        // クエリパラメータから年月を取得（なければ現在の月）
        $monthParam = $request->query('month');
        $currentMonth = Carbon::hasFormat($monthParam, 'Y-m') ? $monthParam : Carbon::now()->format('Y-m');

        // 日付の範囲を計算
        $startDate = Carbon::parse($currentMonth)->startOfMonth();
        $endDate = Carbon::parse($currentMonth)->endOfMonth();

        // 指定されたスタッフの指定月の勤怠情報を取得
        $attendances = Attendance::where('user_id', $staff->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('breaks')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                // 休憩時間の合計を計算
                $totalBreakSeconds = $attendance->breaks->sum(function ($break) {
                    if ($break->break_time) {
                        list($hours, $minutes, $seconds) = explode(':', $break->break_time);
                        return ($hours * 3600) + ($minutes * 60) + $seconds;
                    }
                    return 0;
                });

                // 勤務時間の計算（休憩時間を引く）
                if ($attendance->start_time && $attendance->end_time) {
                    $startTime = Carbon::parse($attendance->start_time);
                    $endTime = Carbon::parse($attendance->end_time);
                    $workDurationSeconds = $endTime->diffInSeconds($startTime);

                    // 休憩時間を引く
                    $netWorkSeconds = max($workDurationSeconds - $totalBreakSeconds, 0);

                    // `H:i:s` に変換
                    $attendance->total_time = gmdate('H:i:s', $netWorkSeconds);
                } else {
                    $attendance->total_time = '-';
                }

                // 休憩時間 `H:i:s` に変換
                $attendance->total_break_time = gmdate('H:i:s', $totalBreakSeconds);

                return $attendance;
            });

        return view('admin.staff_attendance_list', compact('staff', 'attendances', 'currentMonth'));
    }


    public function exportAttendance($id)
    {
        // ここでCSV出力処理を実装（今は未実装のため、プレースホルダー）
        return response()->json(['message' => 'CSV出力機能は未実装です'], 200);
    }

    public function applicationIndex(Request $request)
    {
        $status = $request->query('status', 'pending');

        $applications = AttendanceRequestModel::where('request_status', $status)
            ->with('user', 'attendance')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.application_list', compact('applications', 'status'));
    }

    public function showApplicationDetail(Request $request, $id)
    {
        $status = $request->query('status', 'pending');

        $application = AttendanceRequestModel::with(['user', 'attendance.breaks'])
        ->findOrFail($id);

        return view('admin.application_approval', compact('application', 'status'));
    }



    public function approve($id)
    {
        $application = AttendanceRequestModel::with(['attendance.breaks'])->findOrFail($id);

        if ($application->attendance) {
            $attendance = $application->attendance;

            // NULL の場合は元の値を保持
            $attendance->start_time = $application->start_time ?? $attendance->start_time;
            $attendance->end_time = $application->end_time ?? $attendance->end_time;
            $attendance->remarks = $application->reason;
            $attendance->save();

            // 既存の休憩データを削除
            $attendance->breaks()->delete();

            // 新しい休憩データを保存
            foreach ($attendance->breaks as $break) {
                $attendance->breaks()->create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $break->break_start,
                    'break_end' => $break->break_end,
                    'break_time' => $break->break_time,
                ]);
            }

            // 最新のデータを取得
            $attendance->refresh();
        }

        // 申請のステータスを "approved" に更新
        $application->update([
            'request_status' => 'approved',
        ]);

        return redirect()->route('admin.applications.index')->with('success', '申請を承認しました。');
    }



}
