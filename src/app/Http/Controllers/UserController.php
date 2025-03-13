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


        if ($status === 'working') {
            Attendance::create([
                'user_id' => $user->id,
                'date' => $now->toDateString(),
                'start_time' => $now->toTimeString(),
                'status' => 'working',
            ]);
        }

        elseif ($status === 'on_break') {
            $attendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('date', today())
                                    ->first();

            if ($attendance) {
                $breakStart = Carbon::now()->toTimeString();

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $breakStart,
                ]);


                $attendance->update(['status' => 'on_break']);
            }
        }

        elseif ($status === 'working_again') {
            $attendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('date', today())
                                    ->first();

            if ($attendance) {
                $break = BreakTime::where('attendance_id', $attendance->id)
                                ->whereNull('break_end')
                                ->latest()
                                ->first();

                if ($break) {
                    $breakEnd = Carbon::now()->toTimeString();
                    $breakStart = Carbon::parse($break->break_start);
                    $breakTime = $breakStart ? $breakStart->diff(Carbon::now())->format('%H:%I:%S') : null;

                    $break->update([
                        'break_end' => $breakEnd ?? null,
                        'break_time' => $breakTime ?? null,
                    ]);


                    $attendance->update(['status' => 'working']);
                }
            }
        }

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

        $monthParam = $request->query('month');
        $currentMonth = Carbon::hasFormat($monthParam, 'Y-m') ? $monthParam : Carbon::now()->format('Y-m');


        $startDate = Carbon::parse($currentMonth)->startOfMonth();
        $endDate = Carbon::parse($currentMonth)->endOfMonth();


        $attendances = Attendance::where('user_id', Auth::id())
            ->whereBetween('date', [$startDate, $endDate])
            ->whereDoesntHave('attendanceRequests', function ($query) {
                $query->where('request_status', 'pending');
            })
            ->with('breaks')
            ->orderBy('date', 'asc')
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

        return view('user.attendance_list', compact('attendances', 'currentMonth'));
    }




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


        $existingRequest = AttendanceRequestModel::where('attendance_id', $attendance->id)
            ->where('user_id', Auth::id())
            ->where('request_status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('attendance.detail', $id)->with('error', '既に承認待ちの修正申請があります。');
        }


        $application = AttendanceRequestModel::create([
            'attendance_id' => $attendance->id,
            'user_id' => Auth::id(),
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reason' => $validated['remarks'],
            'request_status' => 'pending',
        ]);

        BreakTime::where('attendance_id', $attendance->id)->delete();


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

        return redirect()->route('applications.index', ['status' => 'pending']);

    }

    public function applicationIndex(Request $request)
    {
        $status = $request->query('status', 'pending');


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
