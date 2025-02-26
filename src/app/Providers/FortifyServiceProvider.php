<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;





class FortifyServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
            public function toResponse($request)
            {
                return redirect('/attendance/register'); // 登録後のリダイレクト先
            }
        });

        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);

    }


    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::loginView(function () {
            return request()->is('admin/*') ? view('admin.login') : view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(10)->by($throttleKey);
        });

        Fortify::redirects('login', function () {
            $user = Auth::user();
            if (!$user) {
                return '/login';
            }
            return $user->role === 'admin'
                ? '/admin/attendance/list'
                : '/attendance/register';
        });



        Fortify::authenticateUsing(function (LoginRequest $request) {
            $credentials = $request->only('email', 'password');

            // ユーザー検索
            $user = User::where('email', $credentials['email'])->first();

            // ユーザーが存在し、パスワードが一致するか確認
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['ログイン情報が登録されていません。'],
                ]);
            }
            if ($request->is('admin/*') && $user->role !== 'admin') {
                throw ValidationException::withMessages([
                    'email' => ['管理者権限がありません。'],
                ]);
            }
            return $user;
        });



    }
}
