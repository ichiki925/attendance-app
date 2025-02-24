<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest as AttendanceRequestModel;
use Carbon\Carbon;
use App\Http\Requests\AttendanceRequest;

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
                // 休憩時間を合計（数値変換後）
                $totalBreakSeconds = $attendance->breaks->sum(function ($break) {
                    if ($break->break_time) {
                        list($hours, $minutes, $seconds) = explode(':', $break->break_time);
                        return ($hours * 3600) + ($minutes * 60) + $seconds;
                    }
                    return 0;
                });

                // 勤務時間の計算
                if ($attendance->start_time && $attendance->end_time) {
                    $startTime = Carbon::parse($attendance->start_time);
                    $endTime = Carbon::parse($attendance->end_time);
                    $workDurationSeconds = $endTime->diffInSeconds($startTime); // 出勤時間の合計（秒）

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


        return view('user.attendance_list', compact('attendances', 'currentMonth'));
    }

    // 勤怠詳細ページ
    public function attendanceDetail($id)
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('id', $id)
            ->with('breaks')
            ->firstOrFail();

        return view('user.attendance_detail', compact('attendance'));
    }

    public function updateAttendance(AttendanceRequest $request, $id)
    {
        $validated = $request->validated();

        $attendance = Attendance::where('user_id', Auth::id())
            ->where('id', $id)
            ->with('breaks')
            ->firstOrFail();

        // 既存の「承認待ち」の修正申請があるか確認
        $existingRequest = AttendanceRequestModel::where('attendance_id', $attendance->id)
            ->where('user_id', Auth::id())
            ->where('request_status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('attendance.detail', $id)->with('error', '既に承認待ちの修正申請があります。');
        }

        // **修正申請を作成（勤怠データは変更しない）**
        $application = AttendanceRequestModel::create([
            'attendance_id' => $attendance->id,
            'user_id' => Auth::id(),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reason' => $validated['remarks'],
            'request_status' => 'pending', // デフォルトで「承認待ち」
        ]);

        BreakTime::where('attendance_id', $attendance->id)->delete();

        // **休憩情報の登録**
        if (!empty($validated['breaks'])) {
            foreach ($validated['breaks'] as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $break['start'],
                        'break_end' => $break['end'],
                        'break_time' => gmdate('H:i:s', strtotime($break['end']) - strtotime($break['start'])),
                    ]);
                }
            }
        }

        return redirect()->route('applications.index', ['status' => 'pending']);

    }

    public function applicationIndex(Request $request)
    {
        $status = $request->query('status', 'pending'); // クエリパラメータから status を取得（デフォルトは 'pending'）

        // 承認待ち or 承認済み のデータを取得
        $applications = AttendanceRequestModel::where('user_id', Auth::id())
            ->when($status, function ($query, $status) {
                return $query->where('request_status', $status);
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.application_list', compact('applications', 'status'));
    }

    public function applicationShow($id)
    {
        $application = AttendanceRequestModel::where('user_id', Auth::id())
            ->where('id', $id)
            ->with(['attendance', 'attendance.breaks'])
            ->firstOrFail();

        $attendance = $application->attendance;

        // ✅ 修正申請の内容があれば、それを適用
        $attendance->remarks = $application->reason ?? $attendance->remarks;

        foreach ($attendance->breaks as $index => $break) {
            $break->break_start = $application->{"breaks_{$index}_start"} ?? $break->break_start;
            $break->break_end = $application->{"breaks_{$index}_end"} ?? $break->break_end;
        }

        if ($application->request_status == 'pending') {
            return view('user.attendance_edit', compact('application'));
        }

        return redirect()->route('attendance.detail', $attendance->id);
    }

}
