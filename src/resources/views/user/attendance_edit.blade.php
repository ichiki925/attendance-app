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
                <td>{{ $application->user->name }}</td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="date-container">
                    <span class="year">{{ \Carbon\Carbon::parse($application->attendance->date)->format('Y') }}年</span>
                    <span class="date">{{ \Carbon\Carbon::parse($application->attendance->date)->format('n月j日') }}</span>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td class="time-container">
                    <span class="start-time">
                        {{ $application->start_time ? \Carbon\Carbon::parse($application->start_time)->format('H:i') : ($application->attendance->start_time ? \Carbon\Carbon::parse($application->attendance->start_time)->format('H:i') : '-') }}
                    </span>
                    <span class="separator">～</span>
                    <span class="end-time">
                        {{ $application->end_time ? \Carbon\Carbon::parse($application->end_time)->format('H:i') : ($application->attendance->end_time ? \Carbon\Carbon::parse($application->attendance->end_time)->format('H:i') : '-') }}
                    </span>
                </td>
            </tr>

            @php
                $breaks = $application->breaks ?? collect();
            @endphp

            @if ($application->attendance && $application->attendance->breaks->count() > 0)
                @foreach ($application->attendance->breaks as $index => $break)
                    <tr>
                        <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                        <td class="time-container">
                            <span class="start-time">
                                {{ \Carbon\Carbon::parse($break->break_start)->format('H:i') }}
                            </span>
                            <span class="separator">～</span>
                            <span class="end-time">
                                {{ \Carbon\Carbon::parse($break->break_end)->format('H:i') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <th>休憩</th>
                    <td>-</td>
                </tr>
            @endif

            <tr>
                <th>備考</th>
                <td>{{ $application->reason ?? $application->attendance->remarks ?? '-' }}</td>
            </tr>
        </table>
    </div>
    <div class="notice-container">
        <p class="notice-text">*承認待ちのため修正はできません。</p>
    </div>
</div>
@endsection
