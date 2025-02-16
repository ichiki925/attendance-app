@extends('layouts.app_user')

@section('title','勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/user/attendance_detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">勤怠詳細</h1>
    </div>
    <div class="detail-container">
    <form action="{{ route('attendance.update', $attendance->id) }}" method="POST">
        @csrf
        <table class="detail-table">
            <tr>
                <th>名前</th>
                <td class="value"><span class="name-padding">{{ $attendance->user->name }}</span></td>
            </tr>
            <tr>
                <th>日付</th>
                <td class="value">
                    <span class="date-year">{{ \Carbon\Carbon::parse($attendance->date)->year }}年</span>
                    <span>{{ \Carbon\Carbon::parse($attendance->date)->month }}月{{ \Carbon\Carbon::parse($attendance->date)->day }}日</span>
                </td>
            </tr>
            <tr>
                <th>出勤・退勤</th>
                <td>
                    <div class="value">
                        <input type="time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($attendance->start_time)->format('H:i')) }}">
                        <span class="symbol">～</span>
                        <input type="time" name="end_time" value="{{ old('end_time', $attendance->end_time ? \Carbon\Carbon::parse($attendance->end_time)->format('H:i') : '') }}">
                    </div>
                        @if ($errors->has('start_time') || $errors->has('end_time'))
                        <div class="error-container">
                            <p class="error-message">出勤時間もしくは退勤時間が不適切な値です</p>
                        </div>
                        @endif
                </td>
            </tr>
            @if ($attendance->breaks->isNotEmpty())
                @foreach ($attendance->breaks as $break)
                <tr>
                    <th>{{ $loop->first ? '休憩' : '休憩' . $loop->iteration }}</th>
                    <td>
                        <div class="value">
                            <input type="time" name="breaks[{{ $loop->index }}][start]" value="{{ old("breaks.{$loop->index}.start", \Carbon\Carbon::parse($break->break_start)->format('H:i')) }}">
                            <span class="symbol">～</span>
                            <input type="time" name="breaks[{{ $loop->index }}][end]" value="{{ old("breaks.{$loop->index}.end", $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
                        </div>
                            @if ($errors->has("breaks.{$loop->index}.start") || $errors->has("breaks.{$loop->index}.end"))
                            <div class="error-container">
                                <p class="error-message">休憩時間が勤務時間外です</p>
                            </div>
                            @endif
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <th>休憩</th>
                    <td class="value">休憩なし</td>
                </tr>
            @endif
            <tr>
                <th>備考</th>
                <td>
                    <div class="value">
                        <textarea name="remarks">{{ old('remarks', $attendance->remarks ?? '') }}</textarea>
                    </div>
                    @error('remarks')
                    <div class="error-container">
                        <p class="error-message">{{ $message }}</p>
                    </div>
                    @enderror
                </td>
            </tr>
        </table>
        <button type="submit" class="edit-button">修正</button>
    </form>
    </div>
</div>
@endsection