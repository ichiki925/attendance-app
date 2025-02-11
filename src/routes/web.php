<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;


// 一般ユーザー用認証
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// 一般ユーザー
Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/list', [UserController::class, 'attendanceIndex'])->name('attendance.list');
    Route::get('/attendance/register', [UserController::class, 'showAttendanceRegister'])->name('attendance.register');

    // 🔽 ここにログアウト処理を追加！ 🔽
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');
});

// 管理者
Route::middleware(['auth', 'redirect.role'])->group(function () {
    Route::get('/admin/attendance/list', [AdminController::class, 'attendanceIndex'])->name('admin.attendance.list');
});



// 管理者用認証
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');



// 管理者用機能
// Route::middleware(['auth:admin'])->group(function () {
//     Route::get('/admin/attendance/list', [AdminController::class, 'attendanceIndex'])->name('admin.attendance.list');
//     Route::get('/admin/attendance/{id}', [AdminController::class, 'show'])->name('admin.attendance.show');
//     Route::get('/admin/staff/list', [AdminController::class, 'staffIndex'])->name('admin.staff.list');
//     Route::get('/admin/staff/{id}', [AdminController::class, 'staffAttendanceIndex'])->name('admin.staff.attendance.detail');
//     Route::get('/admin/stamp_correction_request/list', [AdminController::class, 'applicationIndex'])->name('admin.stamp_correction_request.list');
//     Route::post('/admin/stamp_correction_request/approve/{id}', [AdminController::class, 'approve'])->name('admin.stamp_correction_request.approve');
// });


// 出勤登録画面表示用
    // Route::get('/attendance/register', [UserController::class, 'showAttendanceRegister'])->name('attendance.register');
 // 勤怠一覧ページのルート
 //  Route::get('/attendance/list', [UserController::class, 'attendanceIndex'])->name('attendance.list');
// 勤怠詳細画面表示用
    Route::get('/attendance/detail', [UserController::class, 'showAttendanceDetail'])->name('attendance.detail');
// 勤怠編集画面の表示ルート（応用）
    Route::get('/attendance/edit', [UserController::class, 'showAttendanceEdit'])->name('attendance.edit');
// 申請一覧ページのルート
    Route::get('/application/list', [UserController::class, 'showApplicationList'])->name('application.list');

// 管理者用勤怠一覧ページのルート
    // Route::get('/admin/attendance/list', [AdminController::class, 'attendanceIndex'])->name('admin.attendance.list');
//  管理者用勤怠詳細ページ
    Route::get('/admin/attendance/{id}', [AdminController::class, 'showAttendanceDetail'])->name('admin.attendance.detail');
// 管理者用スタッフ一覧ページのルート
    Route::get('/admin/staff/list', [AdminController::class, 'staffIndex'])->name('admin.staff.list');
// 管理者用スタッフ別勤怠一覧
    Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'staffAttendanceIndex'])->name('admin.staff.attendance.list');
// 申請一覧画面
    Route::get('/stamp_correction_request/list', [AdminController::class, 'applicationIndex'])->name('admin.stamp_correction_request.list');
// 修正申請承認画面
    Route::get('/admin/application/{id}', [AdminController::class, 'showApplicationDetail'])->name('admin.application.detail');
    Route::post('/admin/application/approve/{id}', [AdminController::class, 'approve'])->name('admin.approve');
