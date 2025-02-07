<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function attendanceIndex()
    {
        // ダミーデータを用意
    $attendances = [
        (object)[
            'id' => 1,
            'name' => '山田 太郎',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_time' => '1:00',
            'total_time' => '8:00',
        ],
        (object)[
            'id' => 2,
            'name' => '西 玲奈',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_time' => '1:00',
            'total_time' => '8:00',
        ],
    ];

    // ビューにデータを渡す
    return view('admin.attendance_list', compact('attendances'));
    }

    public function showAttendanceDetail($id)
    {
        return view('admin.attendance_detail', compact('id'));
    }

    public function staffIndex()
    {
        return view('admin.staff.list');
    }

    public function staffAttendanceIndex($id)
    {
        return view('admin.attendance.staff', compact('id'));
    }

    public function applicationIndex()
    {
        return view('admin.stamp_correction_request.list');
    }

    public function approve(Request $request, $id)
    {
        // 修正申請承認処理
    }
}
