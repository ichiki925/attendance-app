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
                        <span class="date">{{ \Carbon\Carbon::parse($application->attendance->date)->format('m月d日') }}</span>
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-container">
                        <span class="start-time">{{ isset($application->start_time) ? \Carbon\Carbon::parse($application->start_time)->format('H:i') : '-' }}</span>
                        <span class="separator">～</span>
                        <span class="end-time">{{ isset($application->end_time) ? \Carbon\Carbon::parse($application->end_time)->format('H:i') : '-' }}</span>
                    </td>
                </tr>

                @php
                    $breaks = $application->attendance->breaks;
                @endphp

                @if ($breaks->isNotEmpty())
                    @foreach ($breaks as $index => $break)
                        <tr>
                            <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                            <td class="time-container">
                                <span class="start-time">
                                    {{ isset($application->{"breaks_{$index}_start"}) && !empty($application->{"breaks_{$index}_start"})
                                        ? \Carbon\Carbon::parse($application->{"breaks_{$index}_start"})->format('H:i')
                                        : \Carbon\Carbon::parse($break->break_start)->format('H:i') }}
                                </span>
                                <span class="separator">～</span>
                                <span class="end-time">
                                    {{ isset($application->{"breaks_{$index}_end"}) && !empty($application->{"breaks_{$index}_end"})
                                        ? \Carbon\Carbon::parse($application->{"breaks_{$index}_end"})->format('H:i')
                                        : ($break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '未終了') }}
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
