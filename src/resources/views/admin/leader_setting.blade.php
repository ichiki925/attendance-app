@extends('layouts.app_admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/leader_setting.css') }}">
@endsection

@section('content')
<div class="leader-setting">
    <div class="header">
        <div class="vertical-line"></div>
        <h2 class="title">リーダー承認設定</h2>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="current-setting">
        <p class="current-label">
            現在の設定：
            <span>{{ $setting ? '設定済み' : '未設定' }}</span>
        </p>
    </div>

    <div class="form-wrapper">
        <p class="form-title">シークレット番号を設定</p>
        <form action="{{ route('admin.leader_setting.update') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>シークレット番号（4〜20文字）</label>
                <input type="password" name="secret_code" class="form-input" required minlength="4" maxlength="20" placeholder="新しい番号を入力">
            </div>
            <button type="submit" class="btn-submit">設定を保存</button>
        </form>
    </div>
</div>
@endsection