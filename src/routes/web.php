<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->middleware('guest:admin')
        ->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('guest:admin');
    // 他の管理者用ルート
});


// 一般ユーザー
Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/register', [UserController::class, 'showAttendanceRegister'])->name('attendance.register');
    Route::post('/attendance/register', [UserController::class, 'storeAttendance'])->name('attendance.store');
    Route::get('/attendance/list', [UserController::class, 'attendanceIndex'])->name('attendance.list');
    Route::get('/attendance/{id}', [UserController::class, 'attendanceDetail'])->name('attendance.detail');
    Route::get('/attendance/{id}/edit', [UserController::class, 'showAttendanceEdit'])->name('attendance.edit');
    Route::post('/attendance/update/{id}', [UserController::class, 'updateAttendance'])->name('attendance.update');
    Route::get('/stamp_correction_request/list', [UserController::class, 'applicationIndex'])->name('applications.index');
    Route::get('/stamp_correction_request/{id}', [UserController::class, 'applicationShow'])->name('applications.show');



    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login'); // 一般ユーザーは `/login` にリダイレクト
    })->name('logout');
});

// 管理者 (認証 + ミドルウェア)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/attendance/list', [AdminController::class, 'attendanceIndex'])->name('attendance.list');
    Route::get('/attendance/{id}', [AdminController::class, 'showAttendanceDetail'])->name('attendance.detail');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');




});



// 管理者用認証
// Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');



// 管理者用機能
// Route::middleware(['auth:admin'])->group(function () {
//     Route::get('/admin/attendance/list', [AdminController::class, 'attendanceIndex'])->name('admin.attendance.list');
//     Route::get('/admin/attendance/{id}', [AdminController::class, 'show'])->name('admin.attendance.show');
//     Route::get('/admin/staff/list', [AdminController::class, 'staffIndex'])->name('admin.staff.list');
//     Route::get('/admin/staff/{id}', [AdminController::class, 'staffAttendanceIndex'])->name('admin.staff.attendance.detail');
//     Route::get('/admin/stamp_correction_request/list', [AdminController::class, 'applicationIndex'])->name('admin.stamp_correction_request.list');
//     Route::post('/admin/stamp_correction_request/approve/{id}', [AdminController::class, 'approve'])->name('admin.stamp_correction_request.approve');
// });





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
