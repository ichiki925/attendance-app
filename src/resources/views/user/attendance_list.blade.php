@extends('layouts.app_user') {{-- レイアウトを指定 --}}

<!-- タイトル -->
@section('title','勤怠一覧')

<!-- css読み込み -->
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
    <div class="month-nav">
        <a href="#" class="prev">← 前月</a>
        <div class="center-content">
            <span class="material-symbols-outlined calendar-icon">calendar_month</span>
            <span class="current-month">2023/06</span>
        </div>
        <a href="#" class="next">翌月 →</a>
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
                @for ($i = 1; $i <= 30; $i++)
                <tr>
                    <td>{{ sprintf('06/%02d', $i) }}({{ ['日', '月', '火', '水', '木', '金', '土'][$i % 7] }})</td>
                    <td>09:00</td>
                    <td>18:00</td>
                    <td>1:00</td>
                    <td>8:00</td>
                    <td><a href="/attendance/{{ $i }}" class="detail-link">詳細</a></td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>
@endsection
