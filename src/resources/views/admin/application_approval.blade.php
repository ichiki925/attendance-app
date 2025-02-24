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
    <div class="detail-container">
        <div class="attendance-table">
            <table>
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
                        <span class="start-time">{{ $application->attendance->start_time ?? '-' }}</span>
                        <span class="separator">～</span>
                        <span class="end-time">{{ $application->attendance->end_time ?? '-' }}</span>
                    </td>
                </tr>
                <tr>
                    <th>休憩</th>
                    <td class="time-container">
                        @if (optional($application->attendance->breaks)->isNotEmpty())
                            @foreach ($application->attendance->breaks as $index => $break)
                                <div class="break-time">
                                    <span class="break-label">休憩{{ $index + 1 }}:</span>
                                    <span class="start-time">{{ $break->break_start ?? '-' }}</span>
                                    <span class="separator">～</span>
                                    <span class="end-time">{{ $break->break_end ?? '-' }}</span>
                                </div>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>{{ $application->reason }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="button-container">
        @if ($application->request_status === 'pending')
        <form method="POST" action="{{ route('admin.application.approve', $application->id) }}">
            @csrf
            <button class="approval-button">承認</button>
        </form>
        @else
        <button class="approved-button" disabled>承認済み</button>
        @endif
    </div>
</div>
@endsection
