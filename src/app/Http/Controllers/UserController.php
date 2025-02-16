<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
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
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('id', $id)
            ->with('breaks')
            ->firstOrFail();

        // バリデーション
        $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'remarks' => 'nullable|string|max:255',
        ]);
        // 勤務時間の更新
        $attendance->start_time = $request->input('start_time');
        $attendance->end_time = $request->input('end_time');
        $attendance->remarks = $request->input('remarks');
        $attendance->save();

        // 既存の休憩データを削除（新しいデータで上書き）
        $attendance->breaks()->delete();

        // 新しい休憩データを保存
        if ($request->has('breaks')) {
            foreach ($request->input('breaks') as $breakData) {
                if (!empty($breakData['start']) && !empty($breakData['end'])) {
                    $attendance->breaks()->create([
                        'break_start' => $breakData['start'],
                        'break_end' => $breakData['end'],
                    ]);
                }
            }
        }

        return redirect()->route('attendance.detail', $id)->with('success', '勤怠情報を更新しました。');
    }

    public function applicationIndex()
    {
        $user = Auth::user();

        // ログインユーザーの申請データ取得
        $applications = AttendanceRequest::where('user_id', $user->id)
            ->orderBy('requested_at', 'desc')
            ->get();

        return view('user.application_list', compact('applications'));
    }

    public function applicationShow($id)
    {
        $user = Auth::user();

        // ログインユーザーが作成した申請のみ取得
        $application = AttendanceRequest::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        return view('user.application_detail', compact('application'));
    }













}
