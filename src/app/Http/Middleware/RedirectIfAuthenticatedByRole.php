<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedByRole
{

    public function handle(Request $request, Closure $next)
    {

        if (Auth::check()) {

            if (Auth::user()->role === 'admin') {
                return redirect('/admin/attendance/list'); // 管理者
            }
            return redirect('/attendance/list'); // 一般ユーザー
        }


        return $next($request);
    }
}
