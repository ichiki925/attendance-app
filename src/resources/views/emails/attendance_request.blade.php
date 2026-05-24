<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>勤怠修正申請通知</title>
</head>
<body>
    <p>管理者様</p>
    <p>以下の勤怠修正申請が届きました。</p>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>申請者</th>
            <td>{{ $attendanceRequest->user->name }}</td>
        </tr>
        <tr>
            <th>対象日</th>
            <td>{{ \Carbon\Carbon::parse($attendanceRequest->attendance->date)->format('Y年m月d日') }}</td>
        </tr>
        <tr>
            <th>修正後 出勤時刻</th>
            <td>{{ $attendanceRequest->start_time ?? '-' }}</td>
        </tr>
        <tr>
            <th>修正後 退勤時刻</th>
            <td>{{ $attendanceRequest->end_time ?? '-' }}</td>
        </tr>
        <tr>
            <th>申請理由</th>
            <td>{{ $attendanceRequest->reason ?? '-' }}</td>
        </tr>
    </table>

    <p>管理画面から承認・却下をお願いします。</p>
</body>
</html>