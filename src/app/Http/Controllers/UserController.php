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
        return view('user.attendance_list');
    }

    // public function showAttendance($id)
    // {
    //     return view('user.attendance_detail', compact('id'));
    // }

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
}
