<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
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

    public function staffIndex()
    {
        // ダミーデータを用意
    $staffs = [
        (object)[ 'id' => 1, 'name' => '西 玲奈', 'email' => 'reina.n@coachtech.com' ],
        (object)[ 'id' => 2, 'name' => '山田 太郎', 'email' => 'taro.y@coachtech.com' ],
        (object)[ 'id' => 3, 'name' => '増田 一世', 'email' => 'issei.m@coachtech.com' ],
        (object)[ 'id' => 4, 'name' => '山本 敬吉', 'email' => 'keikichi.y@coachtech.com' ],
        (object)[ 'id' => 5, 'name' => '秋田 朋美', 'email' => 'tomomi.a@coachtech.com' ],
        (object)[ 'id' => 6, 'name' => '中西 教夫', 'email' => 'norio.n@coachtech.com' ],
    ];

    return view('admin.staff_list', compact('staffs'));
    }

    public function staffAttendanceIndex($id)
    {
        // ダミーデータ
        $staff = (object)[
            'id' => $id,
            'name' => '西 玲奈',
        ];

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
