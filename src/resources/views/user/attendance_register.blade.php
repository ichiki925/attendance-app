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
                <div class="type-select">
                    <label for="attendance_type_id">勤怠区分</label>
                    <select name="attendance_type_id" id="attendance_type_id">
                        @foreach($attendanceTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="pattern-select">
                    <label for="work_pattern_id">勤務パターン</label>
                    <select id="work_pattern_id">
                        <option value="">-- 選択してください --</option>
                        @foreach($workPatterns as $pattern)
                            <option value="{{ $pattern->id }}"
                                data-start="{{ \Carbon\Carbon::parse($pattern->start_time)->format('H:i') }}"
                                data-end="{{ \Carbon\Carbon::parse($pattern->end_time)->format('H:i') }}"
                                data-break="{{ $pattern->break_minutes }}">
                                {{ $pattern->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="pattern-info" id="patternInfo" style="display:none;">
                    <p>本日の予定：<span id="patternDetail"></span></p>
                </div>
                <button type="submit">出勤</button>
            </form>
        @elseif ($status === 'working')
            @php
                $todayAttendance = \App\Models\Attendance::where('user_id', Auth::id())
                    ->whereDate('date', today())
                    ->first();
            @endphp

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

            @if($todayAttendance && !$todayAttendance->leader_approved)
                <div class="leader-approve-form">
                    @if(session('error'))
                        <p class="error-msg">{{ session('error') }}</p>
                    @endif
                    <div class="leader-divider"></div>
                    <form action="{{ route('attendance.leader_approve') }}" method="POST">
                        @csrf
                        <div class="leader-input-row">
                            <label>🔑 リーダー承認番号</label>
                            <input type="password" name="secret_code" placeholder="番号を入力" class="leader-input">
                        </div>
                        <button type="submit" class="btn-leader">承認</button>
                    </form>
                </div>
            @elseif($todayAttendance && $todayAttendance->leader_approved)
                <div class="leader-approved-badge">
                    <p class="approved-text">リーダー承認済み</p>
                </div>
            @endif

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
@section('scripts')
<script>
    document.getElementById('work_pattern_id').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const info = document.getElementById('patternInfo');
        const detail = document.getElementById('patternDetail');

        if (this.value) {
            const start = selected.dataset.start;
            const end = selected.dataset.end;
            const breakMin = selected.dataset.break;
            detail.textContent = start + '〜' + end + '（休憩' + breakMin + '分）';
            info.style.display = 'block';
        } else {
            info.style.display = 'none';
        }
    });

</script>
@endsection
