@extends('layouts.app_admin')

@section('title', '管理者勤怠詳細')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin/attendance_detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">勤怠詳細</h1>
    </div>
    <div class="detail-container">
    <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
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

            <tbody id="break-container">
                @php
                    $oldBreaks = old('breaks', $attendance->breaks->toArray());
                @endphp

                @foreach ($oldBreaks as $breakIndex => $break)
                    <tr class="break-row">
                        <th>{{ $breakIndex === 0 ? '休憩' : '休憩' . ($breakIndex + 1) }}</th>
                        <td>
                            <div class="value">
                                <input type="time" name="breaks[{{ $breakIndex }}][break_start]" class="break-start"
                                    value="{{ old("breaks.$breakIndex.break_start", $break['break_start'] ?? '') }}">
                                <span class="symbol">～</span>
                                <input type="time" name="breaks[{{ $breakIndex }}][break_end]" class="break-end"
                                    value="{{ old("breaks.$breakIndex.break_end", $break['break_end'] ?? '') }}">
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
            </tbody>

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

<script>
document.addEventListener("DOMContentLoaded", function () {
    const breakContainer = document.getElementById("break-container");

    let breakCount = document.querySelectorAll(".break-row").length;

    const addNewBreakRow = () => {
        const newRow = document.createElement("tr");
        newRow.classList.add("break-row");

        const breakLabel = breakCount === 0 ? "休憩" : `休憩${breakCount + 1}`;

        newRow.innerHTML = `
            <th>${breakLabel}</th>
            <td>
                <div class="value">
                    <input type="time" name="breaks[${breakCount}][break_start]" class="break-start">
                    <span class="symbol">～</span>
                    <input type="time" name="breaks[${breakCount}][break_end]" class="break-end">
                </div>
            </td>
        `;

        breakContainer.appendChild(newRow);
        breakCount++;
    };

    const ensureEmptyBreakRow = () => {
        const allBreakRows = document.querySelectorAll(".break-row");
        if (allBreakRows.length === 0 ||
            allBreakRows[allBreakRows.length - 1].querySelector(".break-start").value ||
            allBreakRows[allBreakRows.length - 1].querySelector(".break-end").value) {
            addNewBreakRow();
        }
    };

    if (breakCount === 0) {
        addNewBreakRow();
    }

    document.addEventListener("input", (event) => {
        if (event.target.classList.contains("break-start") || event.target.classList.contains("break-end")) {
            ensureEmptyBreakRow();
        }
    });
});

</script>

@endsection