<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
    Route::put('/attendance/update/{id}', [UserController::class, 'updateAttendance'])->name('attendance.update');
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
    Route::put('/attendance/{id}', [AdminController::class, 'updateAttendance'])->name('attendance.update');
    Route::get('/staff/list', [AdminController::class, 'staffIndex'])->name('staff.list');
    Route::get('/attendance/staff/{id}', [AdminController::class, 'staffAttendanceIndex'])->name('staff.attendance.list');
    Route::get('/attendance/staff/{id}/export', [AdminController::class, 'exportAttendance'])
    ->name('attendance.export');
    Route::get('/stamp_correction_request/list', [AdminController::class, 'applicationIndex'])->name('applications.index');
    Route::get('/application/{id}', [AdminController::class, 'showApplicationDetail'])->name('application.detail');
    Route::post('/application/approve/{id}', [AdminController::class, 'approve'])->name('application.approve');


    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');




});








