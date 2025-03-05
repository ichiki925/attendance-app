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
        <h1 class="title">
            {{ \Carbon\Carbon::parse($selectedDate)->isoFormat('YYYY年M月D日') }}の勤怠
        </h1>
    </div>
    <div class="date-nav">
        @php
            $dateObj = !empty($selectedDate) ? \Carbon\Carbon::parse($selectedDate) : null;
        @endphp
        <a href="{{ route('admin.attendance.list', ['date' => $dateObj ? $dateObj->copy()->subDay()->toDateString() : '']) }}" class="prev">← 前日</a>
        <div class="center-content">
            <span class="material-symbols-outlined calendar-icon" id="calendarIcon">calendar_month</span>
            <span class="current-date" id="selectedDateDisplay">
                {{ $dateObj ? $dateObj->format('Y/m/d') : '日付不明' }}
            </span>


            <input type="date" id="datePicker" value="{{ $selectedDate ?? '' }}" class="hidden-date-picker">
        </div>
        <a href="{{ route('admin.attendance.list', ['date' => $dateObj ? $dateObj->copy()->addDay()->toDateString() : '']) }}" class="next">翌日 →</a>
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
                    <td>{{ $attendance->user->name ?? '不明' }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}
                    </td>
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


        document.getElementById('datePicker').addEventListener('change', function() {
            const selectedDate = this.value;
            document.getElementById('selectedDateDisplay').innerText = selectedDate.replace(/-/g, '/');


            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('date', selectedDate);
            window.location.href = currentUrl.toString();
        });

    </script>
    <style>

        .hidden-date-picker {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }
    </style>
</div>
@endsection
