<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>勤怠管理</title>
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
                @if (Auth::check() && Auth::user()->role === 'user')
                <ul>
                    @if (isset($status) && $status === 'done')
                        <li><a href="/attendance/list">今月の出勤一覧</a></li>
                        <li><a href="/stamp_correction_request/list">申請一覧</a></li>
                    @else
                        <li><a href="/attendance/register">勤怠</a></li>
                        <li><a href="/attendance/list">勤怠一覧</a></li>
                        <li><a href="/stamp_correction_request/list">申請</a></li>
                    @endif
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="logout-button">
                                ログアウト
                            </button>
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
