<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <style>
        @font-face {
            font-family: 'NotoSans';
            src: url('/var/www/storage/fonts/NotoSansCJKjp-Regular.ttf') format('truetype');
        }
        body {
            font-family: 'NotoSans', sans-serif;
            font-size: 12px;
            color: #333;
        }
        h1 {
            font-size: 16px;
            margin-bottom: 4px;
        }
        .period {
            font-size: 12px;
            color: #555;
            margin-bottom: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #1a1f36;
            color: #fff;
            padding: 6px;
            text-align: center;
            font-size: 11px;
        }
        td {
            padding: 5px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        .summary {
            margin-top: 16px;
            text-align: right;
            font-size: 12px;
        }
        .summary span {
            margin-left: 20px;
        }
    </style>
</head>
<body>
    <h1>{{ $staff->name }} さんの勤怠票</h1>
    <div class="period">期間：{{ $startDate }} 〜 {{ $endDate }}</div>

    <table>
        <thead>
            <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>残業</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($attendances as $attendance)
            @php $date = \Carbon\Carbon::parse($attendance->date); @endphp
            <tr>
                <td>{{ $date->format('m/d') }}({{ ['日','月','火','水','木','金','土'][$date->dayOfWeek] }})</td>
                <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
                <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}</td>
                <td>{{ $attendance->total_break_time ?? '-' }}</td>
                <td>{{ $attendance->total_time ?? '-' }}</td>
                <td>{{ $attendance->getOvertimeFormatted() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <span>合計時間：{{ $totalTime }}</span>
        <span>時給：{{ number_format($staff->hourly_wage) }}円</span>
        <span>請求金額：{{ number_format($totalAmount) }}円</span>
    </div>
</body>
</html>