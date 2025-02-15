@extends('layouts.app_user')

@section('title','勤怠編集')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/user/attendance_edit.css') }}">
@endsection

@section('content')
<div class="attendance-edit">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">勤怠詳細</h1>
    </div>
    <div class="edit-container">
            <table class="edit-table">
                <tr>
                    <th>名前</th>
                    <td class="value"><span class="name-padding">{{ $attendance->user->name }}</span></td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td class="value">
                        <span class="date-year">{{ \Carbon\Carbon::parse($attendance->date)->year }}年</span>
                        <span>{{ \Carbon\Carbon::parse($attendance->date)->month }}月{{ \Carbon\Carbon::parse($attendance->date)->day }}日</span>
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-container">
                        <span class="start-time">{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }}</span>
                        <span class="separator">～</span>
                        <span class="end-time">{{ $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '未退勤' }}</span>
                    </td>
                </tr>
                @if ($attendance->breaks->isNotEmpty())
                    @foreach ($attendance->breaks as $index => $break)
                    <tr>
                        <th>{{ $loop->first ? '休憩' : '休憩' . $loop->iteration }}</th>
                        <td class="time-container">
                            <span class="start-time">{{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }}</span>
                            <span class="separator">～</span>
                            <span class="end-time">{{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '未終了' }}</span>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <th>休憩</th>
                        <td class="value">休憩なし</td>
                    </tr>
                @endif
                <tr>
                    <th>備考</th>
                    <td>{{ $attendance->remarks ?? '' }}</td>
                </tr>
            </table>
    </div>
    <p class="notice-text">*承認待ちのため修正はできません。</p>
</div>
@endsection
