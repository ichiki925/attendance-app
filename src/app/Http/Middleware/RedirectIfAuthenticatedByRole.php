<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedByRole
{

    public function handle(Request $request, Closure $next)
    {
        // ユーザーがログインしているかチェック
        if (Auth::check()) {
            // ログイン中のユーザーの role に応じてリダイレクト先を設定
            if (Auth::user()->role === 'admin') {
                return redirect('/admin/attendance/list'); // 管理者
            }
            return redirect('/attendance/list'); // 一般ユーザー
        }

        // ログインしていない場合はそのまま次の処理へ
        return $next($request);
    }
}
