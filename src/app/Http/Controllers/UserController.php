<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // 出勤情報を保存する処理
    }

    public function attendanceIndex()
    {
        return view('attendance.list');
    }

    public function showAttendance($id)
    {
        return view('attendance.detail', compact('id'));
    }

    public function applicationIndex()
    {
        return view('stamp_correction_request.list');
    }


    // 出勤登録画面表示
    public function showAttendanceRegister()
    {
        $status = 'not_working'; // ダミー状態
        $time = '08:00'; // 固定の時間
        return view('user.attendance_register', compact('status', 'time'));
    }
}
