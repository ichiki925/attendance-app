@extends('layouts.app_admin')

@section('title', 'スタッフ勤怠一覧')

@section('css')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=calendar_month" />
<link rel="stylesheet" href="{{ asset('/css/admin/staff_attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance_list">
    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:12px 20px;border-radius:6px;margin-bottom:16px;border:1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">{{ $staff->name }}さんの勤怠</h1>
        <span class="hourly-wage-badge">時給：{{ number_format($staff->hourly_wage) }}円</span>
    </div>
    <div class="month-nav">
        <a href="{{ route('admin.staff.attendance.list', ['id' => $staff->id, 'period' => $prevPeriod, 'mode' => $mode]) }}" class="prev">← 前</a>
        <div class="center-content">
            <span class="material-symbols-outlined calendar-icon" id="calendarIcon">calendar_month</span>
            <span class="current-month" id="selectedMonth">{{ $displayLabel }}</span>
            <input type="month" id="monthPicker" value="{{ $periodParam }}" class="hidden-month-picker">
        </div>
        <a href="{{ route('admin.staff.attendance.list', ['id' => $staff->id, 'period' => $nextPeriod, 'mode' => $mode]) }}" class="next">次 →</a>
    </div>

    <div class="mode-toggle">
        <a href="{{ route('admin.staff.attendance.list', ['id' => $staff->id, 'period' => $periodParam, 'mode' => 'closing']) }}"
            class="toggle-btn {{ $mode === 'closing' ? 'active' : '' }}">締め期間</a>
        <a href="{{ route('admin.staff.attendance.list', ['id' => $staff->id, 'period' => $periodParam, 'mode' => 'calendar']) }}"
            class="toggle-btn {{ $mode === 'calendar' ? 'active' : '' }}">カレンダー月</a>
    </div>

    <div class="table-container">
        <table class="attendance-table">
            <thead>
                <tr>
                <th>日付</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩</th>
                <th>合計</th>
                <th>残業</th>
                <th>深夜</th>
                <th>詳細</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                @php
                    $date = \Carbon\Carbon::parse($attendance->date);
                @endphp
                <tr>
                    <td>{{ $date->format('m/d') }}({{ ['日', '月', '火', '水', '木', '金', '土'][$date->dayOfWeek] }})</td>
                    <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
                    <td>
                        @if (!is_null($attendance->end_time))
                            {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if (!is_null($attendance->total_break_time))
                            {{ $attendance->total_break_time }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if (!is_null($attendance->total_time))
                            {{ $attendance->total_time }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        {{ $attendance->getOvertimeFormatted() }}
                    </td>
                    <td>
                        {{ $attendance->getLateNightFormatted() }}
                    </td>
                    <td><a href="{{ route('admin.attendance.detail', $attendance->id) }}" class="detail-link">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="export-btn-container">
        <form method="POST" action="{{ route('admin.staff.attendance.sendInvoice', ['id' => $staff->id]) }}" style="display:inline;">
            @csrf
            <input type="hidden" name="period" value="{{ $periodParam }}">
            <input type="hidden" name="mode" value="{{ $mode }}">
            <button type="submit" class="export-btn" style="background-color:#2d6a4f;margin-right:8px;">請求書メール送信</button>
        </form>
        <form method="GET" action="{{ route('admin.staff.attendance.exportPdf', ['id' => $staff->id]) }}" style="display:inline;">
            <input type="hidden" name="period" value="{{ $periodParam }}">
            <input type="hidden" name="mode" value="{{ $mode }}">
            <button type="submit" class="export-btn" style="background-color:#1a1f36;margin-right:8px;">PDF出力</button>
        </form>
        <form method="GET" action="{{ route('admin.staff.attendance.export', ['id' => $staff->id, 'month' => $currentMonth]) }}" style="display:inline;">
            <select name="format" id="csvFormat" class="hidden-select">
                <option value="utf8" selected>UTF-8</option>
                <option value="sjis">Shift-JIS</option>
            </select>
            <button type="submit" class="export-btn" id="exportBtn">CSV出力</button>
        </form>
    </div>
</div>
<script>
document.getElementById('calendarIcon').addEventListener('click', function(event) {
    const monthPicker = document.getElementById('monthPicker');


    const rect = event.target.getBoundingClientRect();


    monthPicker.style.position = 'absolute';
    monthPicker.style.left = `${rect.left}px`;
    monthPicker.style.top = `${rect.bottom + window.scrollY}px`;


    monthPicker.style.opacity = '1';
    monthPicker.style.pointerEvents = 'auto';

    monthPicker.showPicker();
});


document.getElementById('monthPicker').addEventListener('change', function() {
    const selectedMonth = this.value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('period', selectedMonth);
    window.location.href = currentUrl.toString();
});

document.getElementById('exportBtn').addEventListener('click', function(event) {
    if (event.shiftKey) {
        document.getElementById('csvFormat').value = 'sjis';
    }
});
</script>

@endsection
