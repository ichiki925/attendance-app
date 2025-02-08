@extends('layouts.app_admin') {{-- レイアウトを指定 --}}

<!-- タイトル -->
@section('title', '勤怠詳細')

<!-- CSS読み込み -->
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
                    <td>{{ $attendance->name }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td class="date-container">
                        <span class="year">{{ $attendance->year }}年</span>
                        <span class="date">{{ $attendance->date }}</span>
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-container">
                        <span class="start-time">{{ $attendance->start_time }}</span>
                        <span class="separator">～</span>
                        <span class="end-time">{{ $attendance->end_time }}</span>
                    </td>
                </tr>
                <tr>
                    <th>休憩</th>
                    <td class="time-container">
                        <span class="start-time">{{ $attendance->break_start }}</span>
                        <span class="separator">～</span>
                        <span class="end-time">{{ $attendance->break_end }}</span>
                    </td>
                </tr>
                <tr>
                    <th>休憩2</th>
                    <td>{{ $attendance->break2 ?? '' }}</td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>{{ $attendance->note }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="button-container">
        @if ($attendance->status === 'pending')
        <form method="POST" action="{{ route('admin.approve', $attendance->id) }}">
            @csrf
            <button class="approval-button">承認</button>
        </form>
        @else
        <button class="approved-button" disabled>承認済み</button>
        @endif
    </div>
</div>
@endsection
