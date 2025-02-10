<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>会員登録</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}" />

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
        <h1 class="title">会員登録</h1>

        <form class="form" action="/register" method="post">
            @csrf
            <div class="form-group">
                <label class="form-label">名前</label>
                <input type="text" class="form-input" name="name" value="{{ old('name') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">メールアドレス</label>
                <input type="email" class="form-input" name="email" value="{{ old('email') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">パスワード</label>
                <input type="password" class="form-input" name="password" required>
            </div>

            <div class="form-group">
                <label class="form-label">パスワード確認</label>
                <input type="password" class="form-input" name="password_confirmation" required>
            </div>

            <button type="submit" class="submit-btn">登録する</button>
        </form>

        <a href="#" class="login-link">ログインはこちら</a>
    </div>
</body>
</html>