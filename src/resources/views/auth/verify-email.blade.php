<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メール認証</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="logo">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo">
            </div>
        </div>
    </header>

    <main class="main-content">

        <p>登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>
        
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="verify-button">認証はこちらから</button>
        </form>

        <a href="#" class="resend-link">認証メールを再送する</a>
    </main>

</body>
</html>
