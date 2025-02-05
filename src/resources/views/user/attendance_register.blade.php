@extends('layouts.app_user') {{-- レイアウトを指定 --}}

<!-- タイトル -->
@section('title','出勤登録')

<!-- css読み込み -->
@section('css')
<link rel="stylesheet" href="{{ asset('/css/user/attendance_register.css')  }}" >
@endsection

@section('content')
<div class="attendance">
    <div class="status">
        <span class="status-label">
            @if ($status === 'not_working') 勤務外
            @elseif ($status === 'working') 出勤中
            @elseif ($status === 'break') 休憩中
            @elseif ($status === 'done') 退勤済
            @endif
        </span>
    </div>
    <div class="date-time">
        <p>{{ date('Y年n月j日(D)') }}</p>
        <h2>{{ $time }}</h2>
    </div>
    <div class="actions">
        {{-- 状態に応じたボタンやメッセージを表示 --}}
        @if ($status === 'not_working')
            <button>出勤</button>
        @elseif ($status === 'working')
            <button>退勤</button>
            <button>休憩入</button>
        @elseif ($status === 'break')
            <button>休憩戻</button>
        @elseif ($status === 'done')
            <p>お疲れ様でした。</p>
        @endif
    </div>
</div>
@endsection
