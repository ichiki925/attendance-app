<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class UserController extends Controller
{
     // 出勤登録画面表示
    public function showAttendanceRegister()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', today())
                                ->latest()
                                ->first();

        $status = $attendance ? $attendance->status : 'off_duty';

        return view('user.attendance_register', compact('status'));
    }

    public function storeAttendance(Request $request)
    {
        $user = Auth::user();
        $status = $request->input('status');
        $now = Carbon::now();

        // 出勤処理
        if ($status === 'working') {
            Attendance::create([
                'user_id' => $user->id,
                'date' => $now->toDateString(),
                'start_time' => $now->toTimeString(),
                'status' => 'working',
            ]);
        }
        // 休憩開始処理
        elseif ($status === 'on_break') {
            $attendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('date', today())
                                    ->first();

            if ($attendance) {
                BreakTime::create([ // 修正：BreakTimeモデルを使用
                    'attendance_id' => $attendance->id,
                    'break_start' => $now->toTimeString(),
                ]);

                // 勤怠ステータスを「on_break」に更新
                $attendance->update(['status' => 'on_break']);
            }
        }
        // 休憩終了処理
        elseif ($status === 'working_again') {
            $attendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('date', today())
                                    ->first();

            if ($attendance) {
                $break = BreakTime::where('attendance_id', $attendance->id) // 修正：BreakTimeを使用
                                ->whereNull('break_end')
                                ->latest()
                                ->first();

                if ($break) {
                    $breakEnd = $now->toTimeString();
                    $breakStart = Carbon::parse($break->break_start);
                    $breakTime = $breakStart->diff($now)->format('%H:%I:%S');

                    $break->update([
                        'break_end' => $breakEnd,
                        'break_time' => $breakTime,
                    ]);

                    // 勤怠ステータスを「working」に戻す
                    $attendance->update(['status' => 'working']);
                }
            }
        }
        // 退勤処理
        elseif ($status === 'completed') {
            $attendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('date', today())
                                    ->first();

            if ($attendance) {
                $endTime = $now->toTimeString();
                $startTime = Carbon::parse($attendance->start_time);
                $totalTime = $startTime->diff($now)->format('%H:%I:%S');

                $attendance->update([
                    'end_time' => $endTime,
                    'total_time' => $totalTime,
                    'status' => 'completed',
                ]);
            }
        }

        return redirect()->route('attendance.register');
    }

    public function attendanceIndex(Request $request)
    {
        // クエリパラメータから年月を取得（なければ現在の月）
        $monthParam = $request->query('month');
        $currentMonth = Carbon::hasFormat($monthParam, 'Y-m') ? $monthParam : Carbon::now()->format('Y-m');

        // 日付の範囲を計算
        $startDate = Carbon::parse($currentMonth)->startOfMonth();
        $endDate = Carbon::parse($currentMonth)->endOfMonth();

        // ログインユーザーの指定月の勤怠情報を取得
        $attendances = Attendance::where('user_id', Auth::id())
            ->whereBetween('date', [$startDate, $endDate])
            ->with('breaks')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
        $attendance->total_break_time = $attendance->breaks->sum(fn($break) => (float) $break->break_time);
        return $attendance;
        });

        return view('user.attendance_list', compact('attendances', 'currentMonth'));
    }

    // 勤怠詳細ページ
    public function attendanceDetail($id)
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        return view('user.attendance_detail', compact('attendance'));
    }



    public function applicationIndex()
    {
        return view('stamp_correction_request.list');
    }



    // 勤怠詳細画面表示
    public function showAttendanceDetail()
    {
        return view('user.attendance_detail');
    }
    // 修正承認待ち（応用）
    public function showAttendanceEdit()
    {
        return view('user.attendance_edit');
    }
    // 申請一覧画面表示
    public function showApplicationList()
    {
        return view('user.application_list');
    }


}
