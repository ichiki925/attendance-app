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
        @method('PUT')

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
                                <p class="error-message">
                                    {{ $errors->first('start_time') ?? $errors->first('end_time') }}
                                </p>
                            </div>
                        @endif
                </td>
            </tr>
                @php $breakIndex = 0; @endphp
                @foreach ($attendance->breaks as $breakIndex => $break)
                <tr>
                    <th>{{ $breakIndex === 0 ? '休憩' : '休憩' . ($breakIndex + 1) }}</th>
                    <td>
                        <div class="value">
                            <input type="time" name="breaks[{{ $breakIndex }}][start]"
                                value="{{ old("breaks.$breakIndex.start", $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '') }}">
                            <span class="symbol">～</span>
                            <input type="time" name="breaks[{{ $breakIndex }}][end]"
                                value="{{ old("breaks.$breakIndex.end", $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '') }}">
                        </div>
                            @if ($errors->has("breaks.$breakIndex.start") || $errors->has("breaks.$breakIndex.end"))
                                <div class="error-container">
                                    <p class="error-message">
                                        {{ $errors->first("breaks.$breakIndex.start") ?? $errors->first("breaks.$breakIndex.end") }}
                                    </p>
                                </div>
                            @endif
                    </td>
                </tr>
                @endforeach
            <tr>
                <th>休憩{{ count($attendance->breaks) + 1 }}</th>
                <td>
                    <div class="value">
                        <input type="time" name="breaks[{{ count($attendance->breaks) }}][start]"
                            value="{{ old("breaks." . count($attendance->breaks) . ".start", '') }}">
                        <span class="symbol">～</span>
                        <input type="time" name="breaks[{{ count($attendance->breaks) }}][end]"
                            value="{{ old("breaks." . count($attendance->breaks) . ".end", '') }}">
                    </div>
                </td>
            </tr>
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