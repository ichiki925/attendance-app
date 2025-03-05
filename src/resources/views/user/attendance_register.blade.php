@extends('layouts.app_user')


@section('title','出勤登録')


@section('css')
<link rel="stylesheet" href="{{ asset('/css/user/attendance_register.css')  }}" >
@endsection

@section('content')
<div class="attendance">
    <div class="status">
        <span class="status-label">
            @if ($status === 'off_duty') 勤務外
            @elseif ($status === 'working') 出勤中
            @elseif ($status === 'on_break') 休憩中
            @elseif ($status === 'completed') 退勤済
            @endif
        </span>
    </div>
    <div class="date-time">
        <p>{{ now()->locale('ja')->translatedFormat('Y年n月j日(D)') }}</p>
        <h2>{{ now()->format('H:i') }}</h2>
    </div>
    <div class="actions">
        {{-- 状態に応じたボタンやメッセージを表示 --}}
        @if ($status === 'off_duty')
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="working">
                <button type="submit">出勤</button>
            </form>
        @elseif ($status === 'working')
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="completed">
                <button type="submit">退勤</button>
            </form>
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="on_break">
                <button type="submit">休憩入</button>
            </form>
        @elseif ($status === 'on_break')
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf
                <input type="hidden" name="status" value="working_again">
                <button type="submit">休憩戻</button>
            </form>
        @elseif ($status === 'completed')
            <p>&nbsp;&nbsp;&nbsp;お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection
