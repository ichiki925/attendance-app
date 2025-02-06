@extends('layouts.app_user') {{-- レイアウトを指定 --}}

<!-- タイトル -->
@section('title','勤怠編集')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/user/attendance_edit.css') }}">
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
                    <td>西 伶奈</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td class="date-container">
                        <span class="year">2023年</span>
                        <span class="date">6月1日</span>
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-container">
                        <span class="start-time">09:00</span>
                        <span class="separator">～</span>
                        <span class="end-time">18:00</span>
                    </td>
                </tr>
                <tr>
                    <th>休憩</th>
                    <td class="time-container">
                        <span class="start-time">12:00</span>
                        <span class="separator">～</span>
                        <span class="end-time">13:00</span>
                    </td>
                </tr>
                <tr>
                    <th>休憩2</th>
                    <td></td>
                </tr>
                <tr>
                    <th>備考</th>
                    <td>電車遅延のため</td>
                </tr>
            </table>
        </div>
    </div>
    <p class="notice-text">*承認待ちのため修正はできません。</p>
</div>
@endsection
