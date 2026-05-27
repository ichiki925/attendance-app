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
    <header>
        <div class="container">
            <div class="logo">
                <div class="logo-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                        fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                </div>
                <span class="logo-text">
                    Attend<span class="logo-accent">Track</span>
                </span>
            </div>
        </div>
    </header>

    <main class="main-content">
        <p>登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>

        <a href="http://localhost:8025" class="verify-button">認証はこちらから</a>

        <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
            @csrf
            <button type="submit" class="resend-button">認証メールを再送する</button>
        </form>

        @if (auth()->user() && auth()->user()->hasVerifiedEmail())
            <script>
                window.location.href = "{{ route('attendance.register') }}";
            </script>
        @endif
    </main>

</body>
</html>