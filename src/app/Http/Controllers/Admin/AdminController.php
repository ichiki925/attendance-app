<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use App\Http\Requests\AttendanceRequest;
use Carbon\Carbon;

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

    public function staffAttendanceIndex($id)
    {
        $staff = User::where('role', '!=', 'admin')->findOrFail($id);
        return view('admin.staff_attendance_list', ['staff_name' => $staff->name]);
    }

    public function applicationIndex()
    {
        // ダミーデータ
        $applications = [
            (object)[ 'status' => '承認待ち', 'name' => '西 伶奈', 'target_date' => '2023/06/01', 'reason' => '遅延のため', 'application_date' => '2023/06/02' ],
            (object)[ 'status' => '承認待ち', 'name' => '山田 太郎', 'target_date' => '2023/06/01', 'reason' => '遅延のため', 'application_date' => '2023/08/02' ],
            (object)[ 'status' => '承認待ち', 'name' => '山田 花子', 'target_date' => '2023/06/02', 'reason' => '遅延のため', 'application_date' => '2023/07/02' ],
        ];

        return view('admin.application_list', compact('applications'));
    }

    public function showApplicationDetail($id)
    {
        $attendance = (object)[
            'id' => $id,
            'name' => '西 伶奈',
            'year' => '2023',
            'date' => '6月1日',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'break2' => null,
            'note' => '電車遅延のため',
            'status' => 'pending',
        ];

        return view('admin.application_approval', compact('attendance'));
    }

    public function approve($id)
    {
        // 承認処理を実装
        return redirect()->route('admin.application.detail', $id)->with('success', '申請を承認しました。');
    }


    // public function approve(Request $request, $id)
    // {
    //     // 修正申請承認処理
    // }
}
