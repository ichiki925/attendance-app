<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\AttendanceApprovedNotification;
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

        $attendances = Attendance::where('date', $selectedDate)
            ->whereHas('user', function ($query) {
                $query->where('role', '!=', 'admin');
            })
            ->whereDoesntHave('attendanceRequests', function ($query) {
                $query->where('request_status', 'pending');
            })
            ->with('user', 'breaks')
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($attendance) {
                $attendance->calculateTimes();
                return $attendance;
            });

        return view('admin.attendance_list', compact(
            'attendances', 'selectedDate'
        ));
    }

    public function showAttendanceDetail($id)
    {
        $attendance = Attendance::with(['user', 'breaks'])->findOrFail($id);
        return view('admin.attendance_detail', compact('attendance'));
    }


    public function updateAttendance(AttendanceRequest $request, $id)
    {


        $validated = $request->validated();


        $attendance = Attendance::with('breaks')->findOrFail($id);



        $attendance->start_time = $validated['start_time'];
        $attendance->end_time = $validated['end_time'] ?? null;
        $attendance->remarks = $validated['remarks'] ?? null;
        $attendance->save();


        $attendance->breaks()->delete();


        if (!empty($validated['breaks'])) {
            foreach ($validated['breaks'] as $break) {
                if (!empty($break['break_start']) && !empty($break['break_end'])) {
                    $breakStart = Carbon::parse($break['break_start']);
                    $breakEnd = Carbon::parse($break['break_end']);
                    $breakDurationMinutes = ceil($breakStart->diffInSeconds($breakEnd) / 60);

                    $breakHours = floor($breakDurationMinutes / 60);
                    $breakMinutes = $breakDurationMinutes % 60;

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => $breakStart->format('H:i'),
                        'break_end' => $breakEnd->format('H:i'),
                        'break_time' => sprintf('%02d:%02d', $breakHours, $breakMinutes),
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

    public function updateStaffWage(Request $request, $id)
    {
        $request->validate([
            'hourly_wage' => 'required|integer|min:0|max:9999999',
        ]);

        $staff = User::where('role', '!=', 'admin')->findOrFail($id);
        $staff->hourly_wage = $request->hourly_wage;
        $staff->save();

        return redirect()->route('admin.staff.list')->with('success', '時給を更新しました。');
    }

    public function staffAttendanceIndex(Request $request, $id)
    {
        $staff = User::where('role', '!=', 'admin')->findOrFail($id);

        $mode = $request->query('mode', 'closing');
        $periodParam = $request->query('period', Carbon::now()->format('Y-m'));


        if ($mode === 'calendar') {
            // カレンダー月モード（既存の動作）
            $startDate = Carbon::parse($periodParam)->startOfMonth();
            $endDate   = Carbon::parse($periodParam)->endOfMonth();
            $displayLabel = Carbon::parse($periodParam)->format('Y/m');

            $prevPeriod = Carbon::parse($periodParam)->subMonth()->format('Y-m');
            $nextPeriod = Carbon::parse($periodParam)->addMonth()->format('Y-m');
        } else {
            // 締め日ベースモード
            $closing = \App\Models\ClosingDaySetting::getActive();
            $closingDay = $closing->closing_day;

            // periodParam (Y-m) を基準に締め期間を計算
            $base = Carbon::parse($periodParam);

            if ($closingDay === 31) {
                $startDate = $base->copy()->startOfMonth();
                $endDate   = $base->copy()->endOfMonth();
                $displayLabel = $base->format('Y/m') . '（末日締め）';
                $prevPeriod = $base->copy()->subMonth()->format('Y-m');
                $nextPeriod = $base->copy()->addMonth()->format('Y-m');
            } else {
                // 例: 20日締め → 前月21日〜当月20日
                $startDate = $base->copy()->subMonth()->day($closingDay)->addDay()->startOfDay();
                $endDate   = $base->copy()->day($closingDay)->startOfDay();
                $displayLabel = $startDate->format('Y/m/d') . ' 〜 ' . $endDate->format('Y/m/d');
                $prevPeriod = $base->copy()->subMonth()->format('Y-m');
                $nextPeriod = $base->copy()->addMonth()->format('Y-m');
            }
        }

        $attendances = Attendance::where('user_id', $staff->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereDoesntHave('attendanceRequests', function ($query) {
                $query->where('request_status', 'pending');
            })
            ->with('breaks')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                $attendance->calculateTimes();
                return $attendance;
            });

        $currentMonth = $periodParam; // CSV出力用に維持

        return view('admin.staff_attendance_list', compact(
            'staff', 'attendances', 'currentMonth',
            'mode', 'periodParam', 'displayLabel', 'prevPeriod', 'nextPeriod'
        ));

    }


    public function exportAttendance($id, Request $request)
    {
        $format = $request->query('format', 'utf8');
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $attendances = Attendance::where('user_id', $id)
        ->whereBetween('date', [
            Carbon::parse($month . '-01')->startOfMonth(),
            Carbon::parse($month . '-01')->endOfMonth()
        ])
        ->with('breaks')
        ->get()
        ->map(function ($attendance) {
            $attendance->calculateTimes();
            return $attendance;
        });




        $csvData = [];
        $csvData[] = ['日付', '出勤', '退勤', '休憩時間', '合計時間'];

        foreach ($attendances as $attendance) {
            $csvData[] = [
                $attendance->date,
                $attendance->start_time ? Carbon::parse($attendance->start_time)->format('H:i') : '-',
                $attendance->end_time ? Carbon::parse($attendance->end_time)->format('H:i') : '-',
                $attendance->total_break_time ?? '-',
                $attendance->total_time ?? '-',
            ];
        }


        $callback = function () use ($csvData, $format) {
            $file = fopen('php://output', 'w');

            if ($format === 'sjis') {
                stream_filter_prepend($file, 'convert.iconv.UTF-8/CP932//TRANSLIT');
            }

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };


        $fileName = "attendance_{$id}_{$month}.csv";
        $contentType = $format === 'sjis' ? "text/csv; charset=Shift_JIS" : "text/csv; charset=UTF-8";

        return response()->stream($callback, 200, [
            "Content-Type" => $contentType,
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
        ]);
    }

    public function exportPdf($id, Request $request)
    {
        $period = $request->query('period', Carbon::now()->format('Y-m'));
        $mode = $request->query('mode', 'closing');

        $staff = User::where('role', '!=', 'admin')->findOrFail($id);

        $closing = \App\Models\ClosingDaySetting::getActive();
        $closingDay = $closing->closing_day;
        $base = Carbon::parse($period);

        if ($mode === 'calendar') {
            $startDate = $base->copy()->startOfMonth();
            $endDate   = $base->copy()->endOfMonth();
        } else {
            if ($closingDay === 31) {
                $startDate = $base->copy()->startOfMonth();
                $endDate   = $base->copy()->endOfMonth();
            } else {
                $startDate = $base->copy()->subMonth()->day($closingDay)->addDay()->startOfDay();
                $endDate   = $base->copy()->day($closingDay)->startOfDay();
            }
        }

        $attendances = Attendance::where('user_id', $staff->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('breaks')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                $attendance->calculateTimes();
                return $attendance;
            });

        // 合計時間（分）を計算
        $totalMinutes = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->total_time) {
                [$h, $m] = explode(':', $attendance->total_time);
                $totalMinutes += (int)$h * 60 + (int)$m;
            }
        }
        $totalHours = $totalMinutes / 60;
        $totalTime = sprintf('%d:%02d', floor($totalMinutes / 60), $totalMinutes % 60);
        $totalAmount = (int)round($totalHours * $staff->hourly_wage);

        $pdf = app('dompdf.wrapper')->loadView('admin.pdf.attendance_pdf', [
            'staff'       => $staff,
            'attendances' => $attendances,
            'startDate'   => $startDate->format('Y/m/d'),
            'endDate'     => $endDate->format('Y/m/d'),
            'totalTime'   => $totalTime,
            'totalAmount' => $totalAmount,
        ]);

        return $pdf->download("attendance_{$staff->name}_{$period}.pdf");
    }

    public function sendInvoiceMail(Request $request, $id)
    {
        $period = $request->query('period', Carbon::now()->format('Y-m'));
        $mode = $request->query('mode', 'closing');

        $staff = User::where('role', '!=', 'admin')->findOrFail($id);

        $closing = \App\Models\ClosingDaySetting::getActive();
        $closingDay = $closing->closing_day;
        $base = Carbon::parse($period);

        if ($mode === 'calendar') {
            $startDate = $base->copy()->startOfMonth();
            $endDate   = $base->copy()->endOfMonth();
        } else {
            if ($closingDay === 31) {
                $startDate = $base->copy()->startOfMonth();
                $endDate   = $base->copy()->endOfMonth();
            } else {
                $startDate = $base->copy()->subMonth()->day($closingDay)->addDay()->startOfDay();
                $endDate   = $base->copy()->day($closingDay)->startOfDay();
            }
        }

        $attendances = Attendance::where('user_id', $staff->id)
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with('breaks')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {
                $attendance->calculateTimes();
                return $attendance;
            });

        $totalMinutes = 0;
        foreach ($attendances as $attendance) {
            if ($attendance->total_time) {
                [$h, $m] = explode(':', $attendance->total_time);
                $totalMinutes += (int)$h * 60 + (int)$m;
            }
        }
        $totalHours = $totalMinutes / 60;
        $totalTime = sprintf('%d:%02d', floor($totalMinutes / 60), $totalMinutes % 60);
        $totalAmount = (int)round($totalHours * $staff->hourly_wage);

        $pdf = app('dompdf.wrapper')->loadView('admin.pdf.attendance_pdf', [
            'staff'       => $staff,
            'attendances' => $attendances,
            'startDate'   => $startDate->format('Y/m/d'),
            'endDate'     => $endDate->format('Y/m/d'),
            'totalTime'   => $totalTime,
            'totalAmount' => $totalAmount,
        ]);

        $pdfContent = $pdf->output();

        Mail::to($staff->email)->send(new \App\Mail\InvoiceMail(
            $staff,
            $startDate->format('Y/m/d'),
            $endDate->format('Y/m/d'),
            $totalTime,
            $totalAmount,
            $pdfContent
        ));

        return redirect()->back()->with('success', '請求書メールを送信しました。');
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


            $attendance->start_time = $application->start_time ?? $attendance->start_time;
            $attendance->end_time = $application->end_time ?? $attendance->end_time;
            $attendance->remarks = $application->reason;
            $attendance->save();


            $attendance->breaks()->delete();


            foreach ($attendance->breaks as $break) {
                $attendance->breaks()->create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $break->break_start,
                    'break_end' => $break->break_end,
                    'break_time' => $break->break_time,
                ]);
            }


            $attendance->refresh();
        }


        $application->update([
            'request_status' => 'approved',
        ]);

        // 申請者本人にメール通知
        Mail::to($application->user->email)->send(new AttendanceApprovedNotification($application));

        return redirect()->route('admin.applications.index')->with('success', '申請を承認しました。');
    }

    public function closingDayIndex()
    {
        $settings = \App\Models\ClosingDaySetting::all();
        $active = \App\Models\ClosingDaySetting::where('is_active', true)->first();
        return view('admin.closing_day_setting', compact('settings', 'active'));
    }

    public function closingDayUpdate(Request $request)
    {
        $request->validate([
            'closing_day_setting_id' => 'required|exists:closing_day_settings,id',
        ]);

        // 全件をis_active=falseにしてから選択されたものだけtrueに
        \App\Models\ClosingDaySetting::query()->update(['is_active' => false]);
        \App\Models\ClosingDaySetting::where('id', $request->closing_day_setting_id)
            ->update(['is_active' => true]);

        return redirect()->route('admin.closing_day.index')
            ->with('success', '締め日設定を更新しました。');
    }

    public function roundingSettingIndex()
    {
        $setting = \App\Models\RoundingSetting::getActive();
        return view('admin.rounding_setting', compact('setting'));
    }

    public function roundingSettingUpdate(Request $request)
    {
        $request->validate([
            'round_minutes' => 'required|integer|min:1|max:60',
            'round_type'    => 'required|in:floor,ceil,round',
        ]);

        \App\Models\RoundingSetting::query()->update(['is_active' => false]);
        \App\Models\RoundingSetting::updateOrCreate(
            [
                'round_minutes' => $request->round_minutes,
                'round_type'    => $request->round_type,
            ],
            ['is_active' => true]
        );

        return redirect()->route('admin.rounding_setting.index')
            ->with('success', '丸め処理設定を更新しました。');
    }

    public function workPatternIndex()
    {
        $patterns = \App\Models\WorkPattern::all();
        return view('admin.work_pattern_index', compact('patterns'));
    }

    public function workPatternStore(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'start_time'    => 'required|date_format:H:i',
            'end_time'      => 'required|date_format:H:i',
            'break_minutes' => 'required|integer|min:0|max:480',
        ]);

        \App\Models\WorkPattern::create($request->only('name', 'start_time', 'end_time', 'break_minutes'));

        return redirect()->route('admin.work_pattern.index')
            ->with('success', '勤務パターンを追加しました。');
    }

    public function workPatternDestroy($id)
    {
        \App\Models\WorkPattern::findOrFail($id)->delete();

        return redirect()->route('admin.work_pattern.index')
            ->with('success', '勤務パターンを削除しました。');
    }

    public function attendanceTypeIndex()
    {
        $types = \App\Models\AttendanceType::all();
        return view('admin.attendance_type_index', compact('types'));
    }

    public function attendanceTypeStore(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'color'      => 'required|string|max:7',
            'is_paid'    => 'boolean',
            'is_holiday' => 'boolean',
        ]);

        \App\Models\AttendanceType::create([
            'name'       => $request->name,
            'color'      => $request->color,
            'is_paid'    => $request->boolean('is_paid'),
            'is_holiday' => $request->boolean('is_holiday'),
        ]);

        return redirect()->route('admin.attendance_type.index')
            ->with('success', '勤怠区分を追加しました。');
    }

    public function attendanceTypeDestroy($id)
    {
        \App\Models\AttendanceType::findOrFail($id)->delete();

        return redirect()->route('admin.attendance_type.index')
            ->with('success', '勤怠区分を削除しました。');
    }

    public function leaderSettingIndex()
    {
        $setting = \App\Models\LeaderSetting::getActive();
        return view('admin.leader_setting', compact('setting'));
    }

    public function leaderSettingUpdate(Request $request)
    {
        $request->validate([
            'secret_code' => 'required|string|min:4|max:20',
        ]);

        \App\Models\LeaderSetting::query()->update(['is_active' => false]);
        \App\Models\LeaderSetting::create([
            'secret_code' => \Illuminate\Support\Facades\Hash::make($request->secret_code),
            'is_active'   => true,
        ]);

        return redirect()->route('admin.leader_setting.index')
            ->with('success', 'シークレット番号を更新しました。');
    }

    public function summaryIndex(Request $request)
    {
        $summaryService = new \App\Services\AttendanceSummaryService();
        $periodParam = $request->query('period', now()->format('Y-m'));
        $period = $summaryService->getCurrentPeriod($periodParam);
        $summaries = $summaryService->summarizeAllStaff($period['start'], $period['end']);

        return view('admin.summary', compact('summaries', 'period', 'periodParam'));
    }

    public function proxyCreate()
    {
        $staffs = User::where('role', '!=', 'admin')->get();
        return view('admin.proxy_attendance', compact('staffs'));
    }

    public function proxyStore(AttendanceRequest $request)
    {
        $validated = $request->validated();

        $attendance = Attendance::create([
            'user_id'    => $request->user_id,
            'date'       => $request->date,
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'] ?? null,
            'remarks'    => $validated['remarks'] ?? null,
        ]);

        if (!empty($validated['breaks'])) {
            foreach ($validated['breaks'] as $break) {
                if (!empty($break['break_start']) && !empty($break['break_end'])) {
                    $breakStart = Carbon::parse($break['break_start']);
                    $breakEnd   = Carbon::parse($break['break_end']);
                    $breakDurationMinutes = ceil($breakStart->diffInSeconds($breakEnd) / 60);
                    $breakHours   = floor($breakDurationMinutes / 60);
                    $breakMinutes = $breakDurationMinutes % 60;

                    BreakTime::create([
                        'attendance_id' => $attendance->id,
                        'break_start'   => $breakStart->format('H:i'),
                        'break_end'     => $breakEnd->format('H:i'),
                        'break_time'    => sprintf('%02d:%02d', $breakHours, $breakMinutes),
                    ]);
                }
            }
        }

        // calculateTimesを呼んで合計・残業等を計算・保存
        $attendance->load('breaks');
        $attendance->calculateTimes();
        $attendance->save();

        return redirect()->route('admin.proxy.create')
            ->with('success', '代理入力が完了しました。');
    }

}
