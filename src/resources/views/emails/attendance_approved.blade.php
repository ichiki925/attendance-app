<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>勤怠修正承認通知</title>
</head>
<body>
    <p>{{ $attendanceRequest->user->name }} 様</p>
    <p>以下の勤怠修正申請が承認されました。</p>

    <table border="1" cellpadding="8" cellspacing="0">
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

    <p>ご確認ください。</p>
</body>
</html>