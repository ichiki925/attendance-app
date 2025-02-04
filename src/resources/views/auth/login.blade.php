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
                <img src="{{ asset('images/logo.svg') }}" alt="Logo">
            </div>
        </div>
    </header>
    <div class="form-container">
        <form class="login-form">
            <h1 class="login-title">ログイン</h1>
            <div class="form-group">
                <label class="form-label">メールアドレス</label>
                <input type="email" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">パスワード</label>
                <input type="password" class="form-input" required>
            </div>
            <button type="submit" class="submit-button">登録する</button>
            <div>
                <a href="#" class="signup-link">会員登録はこちら</a>
            </div>
        </form>
    </div>
</body>
</html>