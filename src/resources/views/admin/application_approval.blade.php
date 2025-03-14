@extends('layouts.app_admin')

@section('title', '勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin/application_approval.css') }}">
@endsection

@section('content')
<div class="attendance-edit">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">勤怠詳細</h1>
    </div>
    <div class="attendance-container">
        <table class="attendance-table">
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
                        {{ $application->start_time && $application->start_time !== '-' ? \Carbon\Carbon::parse($application->start_time)->format('H:i') : ($application->attendance->start_time ? \Carbon\Carbon::parse($application->attendance->start_time)->format('H:i') : '-') }}
                    </span>
                    <span class="separator">～</span>
                    <span class="end-time">
                        {{ $application->end_time && $application->end_time !== '-' ? \Carbon\Carbon::parse($application->end_time)->format('H:i') : ($application->attendance->end_time ? \Carbon\Carbon::parse($application->attendance->end_time)->format('H:i') : '-') }}
                    </span>
                </td>
            </tr>


            @if ($application->attendance && $application->attendance->breaks->isNotEmpty())
                @foreach ($application->attendance->breaks as $index => $break)
                    <tr>
                        <th>{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</th>
                        <td class="time-container">
                            <span class="start-time">
                                {{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '-' }}
                            </span>
                            <span class="separator">～</span>
                            <span class="end-time">
                                {{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '-' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <th>休憩</th>
                    <td></td>
                </tr>
            @endif

            <tr>
                <th>備考</th>
                <td>{{ $application->reason ?? $application->attendance->remarks ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="button-container">
        @if ($application->request_status === 'pending')
            <form method="POST" action="{{ route('admin.application.approve', $application->id) }}">
            @csrf
                <button type="submit" class="approval-button">承認</button>
            </form>
        @else
            <button class="approved-button" disabled>承認済み</button>
        @endif
    </div>
</div>

@endsection
