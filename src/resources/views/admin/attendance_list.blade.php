@extends('layouts.app_admin')

@section('title','勤怠一覧')

@section('css')
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=calendar_month" />
<link rel="stylesheet" href="{{ asset('css/admin/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance_list">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">{{ \Carbon\Carbon::parse($selectedDate)->isoFormat('YYYY年M月D日') }}の勤怠</h1>
    </div>
    <div class="date-nav">
        <a href="{{ route('admin.attendance.list', ['date' => \Carbon\Carbon::parse($selectedDate)->subDay()->toDateString()]) }}" class="prev">← 前日</a>
        <div class="center-content">
            <span class="material-symbols-outlined calendar-icon" id="calendarIcon">calendar_month</span>
            <span class="current-date" id="selectedDateDisplay">{{ \Carbon\Carbon::parse($selectedDate)->format('Y/m/d') }}</span>

            <!-- カレンダー用の非表示の input -->
            <input type="date" id="datePicker" value="{{ $selectedDate }}" class="hidden-date-picker">
        </div>
        <a href="{{ route('admin.attendance.list', ['date' => \Carbon\Carbon::parse($selectedDate)->addDay()->toDateString()]) }}" class="next">翌日 →</a>
    </div>
    <div class="table-container">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $attendance)
                <tr>
                    <td>{{ $attendance->user->name ?? '不明' }}</td> <!-- ユーザー名 -->
                    <td>{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</td>
                    <td>{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->total_break_time ? \Carbon\Carbon::parse($attendance->total_break_time)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance->total_time ? \Carbon\Carbon::parse($attendance->total_time)->format('H:i') : '-' }}</td>
                    <td><a href="{{ route('admin.attendance.detail', $attendance->id) }}" class="detail-link">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script>
        document.getElementById('calendarIcon').addEventListener('click', function() {
            document.getElementById('datePicker').showPicker();
        });

        // 日付が選択されたら URL を変更してページをリロード
        document.getElementById('datePicker').addEventListener('change', function() {
            const selectedDate = this.value;  // YYYY-MM-DD の形式
            document.getElementById('selectedDateDisplay').innerText = selectedDate.replace(/-/g, '/');

            // URLに `date` パラメータを追加してページを更新
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('date', selectedDate);
            window.location.href = currentUrl.toString();
        });

    </script>
    <style>
        /* 隠し日付ピッカー */
        .hidden-date-picker {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
    </style>
</div>
@endsection
