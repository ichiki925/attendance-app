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
        // ダミーデータを用意
    $staffs = [
        (object)[ 'id' => 1, 'name' => '西 玲奈', 'email' => 'reina.n@coachtech.com' ],
        (object)[ 'id' => 2, 'name' => '山田 太郎', 'email' => 'taro.y@coachtech.com' ],
        (object)[ 'id' => 3, 'name' => '増田 一世', 'email' => 'issei.m@coachtech.com' ],
        (object)[ 'id' => 4, 'name' => '山本 敬吉', 'email' => 'keikichi.y@coachtech.com' ],
        (object)[ 'id' => 5, 'name' => '秋田 朋美', 'email' => 'tomomi.a@coachtech.com' ],
        (object)[ 'id' => 6, 'name' => '中西 教夫', 'email' => 'norio.n@coachtech.com' ],
    ];

    return view('admin.staff_list', compact('staffs'));
    }

    public function staffAttendanceIndex($id)
    {
        // ダミーデータ
        $staff = (object)[
            'id' => $id,
            'name' => '西 玲奈',
        ];

        return view('admin.staff_attendance_list', ['staff_name' => $staff->name]);
    }

    public function applicationIndex()
    {
        // ダミーデータ
        $applications = [
            (object)[ 'status' => '承認待ち', 'name' => '西 伶奈', 'target_date' => '2023/06/01', 'reason' => '遅延のため', 'application_date' => '2023/06/02' ],
            (object)[ 'status' => '承認待ち', 'name' => '山田 太郎', 'target_date' => '2023/06/01', 'reason' => '遅延のため', 'application_date' => '2023/08/02' ],
            (object)[ 'status' => '承認待ち', 'name' => '山田 花子', 'target_date' => '2023/06/02', 'reason' => '遅延のため', 'application_date' => '2023/07/02' ],
        ];

        return view('admin.application_list', compact('applications'));
    }

    public function showApplicationDetail($id)
    {
        $attendance = (object)[
            'id' => $id,
            'name' => '西 伶奈',
            'year' => '2023',
            'date' => '6月1日',
            'start_time' => '09:00',
            'end_time' => '18:00',
            'break_start' => '12:00',
            'break_end' => '13:00',
            'break2' => null,
            'note' => '電車遅延のため',
            'status' => 'pending',
        ];

        return view('admin.application_approval', compact('attendance'));
    }

    public function approve($id)
    {
        // 承認処理を実装
        return redirect()->route('admin.application.detail', $id)->with('success', '申請を承認しました。');
    }


    // public function approve(Request $request, $id)
    // {
    //     // 修正申請承認処理
    // }
}
