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

            @foreach ($attendance->breaks as $breakIndex => $break)
                <tr>
                    <th>{{ $breakIndex === 0 ? '休憩' : '休憩' . ($breakIndex + 1) }}</th>
                    <td>
                        <div class="value">
                            <input type="time" name="breaks[{{ $breakIndex }}][break_start]"
                                value="{{ old("breaks.$breakIndex.break_start", request()->input("breaks.$breakIndex.break_start", $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '')) }}">
                            <span class="symbol">～</span>
                            <input type="time" name="breaks[{{ $breakIndex }}][break_end]"
                                value="{{ old("breaks.$breakIndex.break_end", request()->input("breaks.$breakIndex.break_end", $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '')) }}">
                        </div>
                        @php
                            $startError = $errors->first("breaks.$breakIndex.break_start");
                            $endError = $errors->first("breaks.$breakIndex.break_end");
                        @endphp

                        @if ($startError)
                            <div class="error-container">
                                <p class="error-message">{{ $startError }}</p>
                            </div>
                        @endif

                        @if ($endError)
                            <div class="error-container">
                                <p class="error-message">{{ $endError }}</p>
                            </div>
                        @endif
                    </td>
                </tr>
            @endforeach

            @php $nextIndex = count($attendance->breaks); @endphp
                <tr>
                    <th>休憩{{ $nextIndex + 1 }}</th>
                        <td>
                            <div class="value">
                                <input type="time" name="breaks[{{ $nextIndex }}][break_start]"
                                    value="{{ old("breaks.$nextIndex.break_start", '') }}">
                                <span class="symbol">～</span>
                                <input type="time" name="breaks[{{ $nextIndex }}][break_end]"
                                    value="{{ old("breaks.$nextIndex.break_end", '') }}">
                            </div>
                            @php
                                $startErrorNew = $errors->first("breaks.$nextIndex.break_start");
                                $endErrorNew = $errors->first("breaks.$nextIndex.break_end");
                            @endphp


                            @if ($startErrorNew)
                                <div class="error-container">
                                    <p class="error-message">{{ $startErrorNew }}</p>
                                </div>
                            @endif

                            @if ($endErrorNew)
                                <div class="error-container">
                                    <p class="error-message">{{ $endErrorNew }}</p>
                                </div>
                            @endif

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