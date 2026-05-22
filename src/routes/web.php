<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminController;



Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect('/admin/attendance/list')
            : redirect('/attendance/list');
    }
    return redirect('/login');
});

// 管理者ログイン（ゲスト専用）
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])
        ->middleware('guest:admin')
        ->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('guest:admin');

});


// 一般ユーザー
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance/register', [UserController::class, 'showAttendanceRegister'])->name('attendance.register');
    Route::post('/attendance/register', [UserController::class, 'storeAttendance'])->name('attendance.store');
    Route::get('/attendance/list', [UserController::class, 'attendanceIndex'])->name('attendance.list');
    Route::get('/attendance/{id}', [UserController::class, 'attendanceDetail'])->name('attendance.detail');
    Route::get('/attendance/{id}/edit', [UserController::class, 'showAttendanceEdit'])->name('attendance.edit');
    Route::put('/attendance/update/{id}', [UserController::class, 'updateAttendance'])->name('attendance.update');
    Route::get('/stamp_correction_request/list', [UserController::class, 'applicationIndex'])->name('applications.index');
    Route::get('/stamp_correction_request/{id}', [UserController::class, 'applicationShow'])->name('applications.show');

});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
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
    ->name('staff.attendance.export');
    Route::get('/stamp_correction_request/list', [AdminController::class, 'applicationIndex'])->name('applications.index');
    Route::get('/application/{id}', [AdminController::class, 'showApplicationDetail'])->name('application.detail');
    Route::post('/application/approve/{id}', [AdminController::class, 'approve'])->name('application.approve');
    Route::get('/closing-day', [AdminController::class, 'closingDayIndex'])->name('closing_day.index');
    Route::post('/closing-day', [AdminController::class, 'closingDayUpdate'])->name('closing_day.update');
    Route::get('/rounding-setting', [AdminController::class, 'roundingSettingIndex'])->name('rounding_setting.index');
    Route::post('/rounding-setting', [AdminController::class, 'roundingSettingUpdate'])->name('rounding_setting.update');
    Route::get('/work-pattern', [AdminController::class, 'workPatternIndex'])->name('work_pattern.index');
    Route::post('/work-pattern', [AdminController::class, 'workPatternStore'])->name('work_pattern.store');
    Route::delete('/work-pattern/{id}', [AdminController::class, 'workPatternDestroy'])->name('work_pattern.destroy');
    Route::get('/attendance-type', [AdminController::class, 'attendanceTypeIndex'])->name('attendance_type.index');
    Route::post('/attendance-type', [AdminController::class, 'attendanceTypeStore'])->name('attendance_type.store');
    Route::delete('/attendance-type/{id}', [AdminController::class, 'attendanceTypeDestroy'])->name('attendance_type.destroy');

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});



// メール認証の画面表示
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


// メール認証のリンクをクリックしたときの処理
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance/register'); // 認証完了後のリダイレクト先
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メールの再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証メールを再送しました');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');





