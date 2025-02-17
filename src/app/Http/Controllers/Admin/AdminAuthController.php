<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    // ログイン処理を行うメソッド
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !\Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'ログイン情報が登録されていません',
            ]);
        }

        // 管理者でなければログインさせない
        if ($user->role !== 'admin') {
            throw ValidationException::withMessages([
                'email' => '管理者権限がありません',
            ]);
        }

        Auth::login($user);
        return redirect()->route('admin.attendance.list');
    }

    // ログアウト処理を行うメソッド
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }
}
