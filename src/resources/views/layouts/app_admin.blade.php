<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <span class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
                <span class="logo-text">Attend<span class="logo-accent">Track</span></span>
            </div>
            <nav>
                @if (Auth::check() && Auth::user()->role === 'admin')
                <ul>
                    <li><a href="{{ route('admin.attendance.list') }}">勤怠一覧</a></li>
                    <li><a href="{{ route('admin.summary') }}">締め日集計</a></li>
                    <li><a href="{{ route('admin.proxy.create') }}">代理入力</a></li>
                    <li><a href="{{ route('admin.staff.list') }}">スタッフ一覧</a></li>
                    <li><a href="{{ route('admin.applications.index') }}">申請一覧</a></li>
                    <li><a href="{{ route('admin.closing_day.index') }}">締め日設定</a></li>
                    <li><a href="{{ route('admin.rounding_setting.index') }}">丸め設定</a></li>
                    <li><a href="{{ route('admin.work_pattern.index') }}">勤務パターン</a></li>
                    <li><a href="{{ route('admin.leader_setting.index') }}">リーダー設定</a></li>
                    <li><a href="{{ route('admin.attendance_type.index') }}">区分設定</a></li>
                    <li>
                        <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="logout-button">ログアウト</button>
                        </form>
                    </li>
                </ul>
                @endif
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>
    @yield('scripts')
</body>
</html>
