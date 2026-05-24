@extends('layouts.app_admin')

@section('title', '締め日集計')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance_list.css') }}">
@endsection

@section('content')
<div class="attendance_list">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">締め日集計</h1>
    </div>

    <div class="date-nav">
        <a href="{{ route('admin.summary.index', ['period' => $period['prev']]) }}" class="prev">← 前期間</a>
        <span class="current-date">{{ $period['label'] }}</span>
        <a href="{{ route('admin.summary.index', ['period' => $period['next']]) }}" class="next">次期間 →</a>
    </div>

    <div class="table-container">
        <table class="attendance-table summary-table">
            <thead>
                <tr>
                    <th>スタッフ</th>
                    <th>総労働時間</th>
                    <th>残業時間</th>
                    <th>法定休日出勤</th>
                    <th>アラート</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($summaries as $summary)
                <tr class="{{ $summary['is_overtime_alert'] ? 'alert-row' : '' }}">
                    <td>{{ $summary['staff']->name }}</td>
                    <td>{{ \App\Services\AttendanceSummaryService::formatMinutes($summary['total_work_minutes']) }}</td>
                    <td>{{ \App\Services\AttendanceSummaryService::formatMinutes($summary['total_overtime_minutes']) }}</td>
                    <td>{{ $summary['holiday_work_days'] }}日</td>
                    <td>
                        @if ($summary['is_overtime_alert'])
                            <span class="badge badge-danger">🔴 60h超残業</span>
                        @endif
                        @if ($summary['is_holiday_alert'])
                            <span class="badge badge-warning">🟡 法定休日出勤</span>
                        @endif
                        @if (!$summary['is_overtime_alert'] && !$summary['is_holiday_alert'])
                            <span class="badge badge-ok">✅ 異常なし</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection