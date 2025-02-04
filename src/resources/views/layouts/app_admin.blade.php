<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
</head>
<body>
    <header class="header">
        <div class="logo">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo">
            </div>
        <nav class="header-nav">
            <ul>
                <li><a href="/attendance-list">勤怠一覧</a></li>
                <li><a href="/staff-list">スタッフ一覧</a></li>
                <li><a href="/application-list">申請一覧</a></li>
                <li><a href="/logout">ログアウト</a></li>
            </ul>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
