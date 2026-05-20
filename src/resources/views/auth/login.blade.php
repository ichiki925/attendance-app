<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ログイン</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}" />

</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <span class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </span>
                <span class="logo-text">Attend<span class="logo-accent">Track</span></span>
            </div>
        </div>
    </header>
    <div class="page-bg">
        <div class="card">
            <div class="card-header">
                <div class="card-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#4f7ef8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h1 class="card-title">ログイン</h1>
                <p class="card-sub">アカウントにサインインしてください</p>
            </div>
            <form action="/login" method="post">
                @csrf
                <div class="form-group">
                    <label class="form-label">メールアドレス</label>
                    <input type="text" class="form-input" name="email" value="{{ old('email') }}">
                    @error('email')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">パスワード</label>
                    <input type="password" class="form-input" name="password">
                    @error('password')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="submit-button">ログイン</button>
            </form>
            <div class="signup-wrap">
                アカウントをお持ちでない方は <a href="/register" class="signup-link">会員登録はこちら</a>
            </div>
        </div>
    </div>
</body>
</html>