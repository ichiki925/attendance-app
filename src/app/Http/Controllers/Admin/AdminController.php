<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function attendanceIndex()
    {
        return view('admin.attendance.list');
    }

    public function showAttendance($id)
    {
        return view('admin.attendance.detail', compact('id'));
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
