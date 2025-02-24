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
                    <img src="{{ asset('images/logo.svg') }}" alt="Logo">
            </div>
            <nav>
                @if (Auth::check() && Auth::user()->role === 'admin')
                <ul>
                    <li><a href="{{ route('admin.attendance.list') }}">勤怠一覧</a></li>
                    <li><a href="{{ route('admin.staff.list') }}">スタッフ一覧</a></li>
                    <li><a href="{{ route('admin.applications.index') }}">申請一覧</a></li>
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
</body>
</html>
