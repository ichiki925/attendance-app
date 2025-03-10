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


        $attendances = Attendance::where('date', $selectedDate)
            ->whereHas('user', function ($query) {
                $query->where('role', '!=', 'admin');
            })
            ->with('user', 'breaks')
            ->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($attendance) {

            $totalBreakSeconds = $attendance->breaks->sum(function ($break) {
                if (!empty($break->break_time) && strpos($break->break_time, ':') !== false) {
                    list($hours, $minutes) = explode(':', $break->break_time);
                    return ($hours * 3600) + ($minutes * 60);
                }
                return 0;
            });


            $totalBreakMinutes = ceil($totalBreakSeconds / 60);
            $breakHours = floor($totalBreakMinutes / 60);
            $breakMinutes = $totalBreakMinutes % 60;
            $attendance->total_break_time = sprintf('%02d:%02d', $breakHours, $breakMinutes);


            if ($attendance->start_time && $attendance->end_time) {
                $startTime = Carbon::parse($attendance->start_time);
                $endTime = Carbon::parse($attendance->end_time);
                $workDurationSeconds = $endTime->diffInSeconds($startTime);


                $netWorkSeconds = max($workDurationSeconds - $totalBreakSeconds, 0);

                $totalMinutes = ceil($netWorkSeconds / 60);
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;

                $attendance->total_time = sprintf('%02d:%02d', $hours, $minutes);
            } else {
                $attendance->total_time = '-';
            }

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

    public function staffAttendanceIndex(Request $request, $id)
    {
        $staff = User::where('role', '!=', 'admin')->findOrFail($id);


        $monthParam = $request->query('month');
        $currentMonth = Carbon::hasFormat($monthParam, 'Y-m') ? $monthParam : Carbon::now()->format('Y-m');


        $startDate = Carbon::parse($currentMonth)->startOfMonth();
        $endDate = Carbon::parse($currentMonth)->endOfMonth();


        $attendances = Attendance::where('user_id', $staff->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('breaks')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($attendance) {

                $totalBreakSeconds = $attendance->breaks->sum(function ($break) {
                    if ($break->break_time) {
                        $timeParts = explode(':', $break->break_time);
                        $hours = (int) $timeParts[0];
                        $minutes = (int) $timeParts[1];
                        return ($hours * 3600) + ($minutes * 60);
                    }
                    return 0;
                });


                $totalBreakMinutes = ceil($totalBreakSeconds / 60);
                $breakHours = floor($totalBreakMinutes / 60);
                $breakMinutes = $totalBreakMinutes % 60;
                $attendance->total_break_time = sprintf('%02d:%02d', $breakHours, $breakMinutes);


                if ($attendance->start_time && $attendance->end_time) {
                    $startTime = Carbon::parse($attendance->start_time);
                    $endTime = Carbon::parse($attendance->end_time);
                    $workDurationSeconds = $endTime->diffInSeconds($startTime);


                    $netWorkSeconds = max($workDurationSeconds - $totalBreakSeconds, 0);


                    $totalMinutes = ceil($netWorkSeconds / 60);
                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;

                    $attendance->total_time = sprintf('%02d:%02d', $hours, $minutes);
                } else {
                    $attendance->total_time = '-';
                }

                return $attendance;
            });

        return view('admin.staff_attendance_list', compact('staff', 'attendances', 'currentMonth'));
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

            $totalBreakSeconds = $attendance->breaks->sum(function ($break) {
                if ($break->break_time) {
                    list($hours, $minutes) = explode(':', $break->break_time);
                    return ($hours * 3600) + ($minutes * 60);
                }
                return 0;
            });

            $totalBreakMinutes = ceil($totalBreakSeconds / 60);
            $breakHours = floor($totalBreakMinutes / 60);
            $breakMinutes = $totalBreakMinutes % 60;
            $attendance->total_break_time = sprintf('%02d:%02d', $breakHours, $breakMinutes);


            if ($attendance->start_time && $attendance->end_time) {
                $startTime = Carbon::parse($attendance->start_time);
                $endTime = Carbon::parse($attendance->end_time);
                $workDurationSeconds = $endTime->diffInSeconds($startTime);

                $netWorkSeconds = max($workDurationSeconds - $totalBreakSeconds, 0);
                $totalMinutes = ceil($netWorkSeconds / 60);
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;

                $attendance->total_time = sprintf('%02d:%02d', $hours, $minutes);
            } else {
                $attendance->total_time = '-';
            }

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

        return redirect()->route('admin.applications.index')->with('success', '申請を承認しました。');
    }



}
