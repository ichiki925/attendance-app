<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\LoginRequest;

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


        // 認証試行
        if (Auth::guard('admin')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        // 認証失敗時の処理
        throw ValidationException::withMessages([
            'email' => ['提供された認証情報は記録と一致しません。'],
        ]);
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
