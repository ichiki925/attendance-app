@extends('layouts.app_user')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=calendar_month" />
<link rel="stylesheet" href="{{ asset('/css/user/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance_list">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">勤怠一覧</h1>
    </div>
    @php
        use Carbon\Carbon;
        $current = Carbon::parse($currentMonth);
        $prevMonth = $current->copy()->subMonth()->format('Y-m');
        $nextMonth = $current->copy()->addMonth()->format('Y-m');
    @endphp
    <div class="month-nav">
        <a href="{{ route('attendance.list', ['month' => $prevMonth]) }}" class="prev">← 前月</a>
        <div class="center-content">
            <span class="material-symbols-outlined calendar-icon" id="calendarIcon">calendar_month</span>
            <span class="current-month" id="selectedMonth">{{ $current->format('Y/m') }}</span>
            <input type="month" id="monthPicker" value="{{ $currentMonth }}" class="hidden-month-picker">
        </div>
        <a href="{{ route('attendance.list', ['month' => $nextMonth]) }}" class="next">翌月 →</a>
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
                    <td><a href="{{ route('attendance.detail', $attendance->id) }}" class="detail-link">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
document.getElementById('calendarIcon').addEventListener('click', function(event) {
    const monthPicker = document.getElementById('monthPicker');

    // アイコンの位置を取得
    const rect = event.target.getBoundingClientRect();

    // 位置をアイコンの直下に調整
    monthPicker.style.position = 'absolute';
    monthPicker.style.left = `${rect.left}px`;
    monthPicker.style.top = `${rect.bottom + window.scrollY}px`;

    // 一時的に表示してから `showPicker()` を実行
    monthPicker.style.opacity = '1';
    monthPicker.style.pointerEvents = 'auto';

    monthPicker.showPicker(); // カレンダーを開く
});

// 月を選択したときに、表示を変更し、URLを更新
document.getElementById('monthPicker').addEventListener('change', function() {
    const selectedMonth = this.value;  // YYYY-MM
    document.getElementById('selectedMonth').innerText = selectedMonth.replace('-', '/');

    // URLに `month` パラメータを追加
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('month', selectedMonth);
    window.location.href = currentUrl.toString();
});
</script>

@endsection
