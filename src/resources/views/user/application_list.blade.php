@extends('layouts.app_user') {{-- レイアウトを指定 --}}

<!-- タイトル -->
@section('title','申請一覧')

<!-- CSS読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/user/application_list.css') }}">
@endsection

@section('content')
<div class="application-list">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">申請一覧</h1>
    </div>
    <div class="tabs">
        <button class="tab-button active">承認待ち</button>
        <button class="tab-button">承認済み</button>
    </div>
    <div class="table-container">
        <table class="application-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>承認待ち</td>
                    <td>西 伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href="#" class="detail-link">詳細</a></td>
                </tr>
                <!-- 以降はダミーデータとして複数行追加 -->
                <tr>
                    <td>承認済み</td>
                    <td>西 伶奈</td>
                    <td>2023/06/01</td>
                    <td>遅延のため</td>
                    <td>2023/06/02</td>
                    <td><a href="#" class="detail-link">詳細</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
