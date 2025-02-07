@extends('layouts.app_admin') {{-- 管理者用レイアウト --}}

@section('title', '勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin/attendance_detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">勤怠詳細</h1>
    </div>
    <div class="detail-container">
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td colspan="3" class="value">西 伶奈</td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="value">
                    <input type="text" value="2023年">
                    <input type="text" value="6月1日">
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td class="value">
                    <input type="time" value="09:00">
                    <span class="symbol">～</span>
                    <input type="time" value="20:00">
                </td>
            </tr>
            <tr>
                <th>休憩</th>
                <td class="value">
                    <input type="time" value="12:00">
                    <span class="symbol">～</span>
                    <input type="time" value="13:00">
                </td>
            </tr>
            <tr>
                <th>休憩2</th>
                <td class="value">
                    <input type="time">
                    <span class="symbol">～</span>
                    <input type="time">
                </td>
            </tr>
            <tr>
                <th>備考</th>
                <td colspan="3" class="value">
                    <textarea></textarea>
                </td>
            </tr>
        </table>
    </div>
    <button class="edit-button">修正</button>
</div>
@endsection
