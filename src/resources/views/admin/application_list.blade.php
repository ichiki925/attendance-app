@extends('layouts.app_admin') {{-- レイアウトを指定 --}}

<!-- タイトル -->
@section('title', '申請一覧')

<!-- CSS読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin/application_list.css') }}">
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
                @foreach ($applications as $application)
                <tr>
                    <td>{{ $application->status }}</td>
                    <td>{{ $application->name }}</td>
                    <td>{{ $application->target_date }}</td>
                    <td>{{ $application->reason }}</td>
                    <td>{{ $application->application_date }}</td>
                    <td><a href="#" class="detail-link">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
