@extends('layouts.app_admin')

@section('title','締め日設定')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/closing_day_setting.css') }}">
@endsection

@section('content')
<div class="closing-day-setting">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">締め日設定</h1>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <p class="current-label">
        現在の締め日：<span>{{ $active ? $active->label : '未設定' }}</span>
    </p>

    <form method="POST" action="{{ route('admin.closing_day.update') }}">
        @csrf
        <div class="form-group">
            <select name="closing_day_setting_id">
                @foreach($settings as $setting)
                    <option value="{{ $setting->id }}"
                        {{ $setting->is_active ? 'selected' : '' }}>
                        {{ $setting->label }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-submit">設定を保存</button>
    </form>
</div>
@endsection