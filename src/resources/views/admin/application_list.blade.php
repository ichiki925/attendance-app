@extends('layouts.app_admin')

@section('title', '申請一覧')

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
        <a href="{{ route('admin.applications.index', ['status' => 'pending']) }}"
        class="tab-button {{ ($status ?? 'pending') === 'pending' ? 'active' : '' }}">
            承認待ち
        </a>
        <a href="{{ route('admin.applications.index', ['status' => 'approved']) }}"
        class="tab-button {{ ($status ?? 'pending') === 'approved' ? 'active' : '' }}">
            承認済み
        </a>
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
                    <td>{{ $application->request_status == 'pending' ? '承認待ち' : '承認済み' }}</td>
                    <td>{{ $application->user->name }}</td>
                    <td>{{ optional($application->attendance)->date ? \Carbon\Carbon::parse($application->attendance->date)->format('Y/m/d') : '-' }}</td>
                    <td>{{ $application->reason }}</td>
                    <td>{{ \Carbon\Carbon::parse($application->created_at)->format('Y/m/d') }}</td>
                    <td>
                        <a href="{{ route('admin.application.detail', ['id' => $application->id]) }}" class="detail-link">詳細</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
