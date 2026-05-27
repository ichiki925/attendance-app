<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p>{{ $staff->name }} さん</p>

    <p>お疲れ様です。<br>
    以下の期間の請求書をお送りします。</p>

    <p>
        期間：{{ $startDate }} 〜 {{ $endDate }}<br>
        合計時間：{{ $totalTime }}<br>
        時給：{{ number_format($staff->hourly_wage) }}円<br>
        請求金額：{{ number_format($totalAmount) }}円
    </p>

    <p>添付のPDFをご確認ください。</p>
</body>
</html>