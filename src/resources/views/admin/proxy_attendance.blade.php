@extends('layouts.app_admin')

@section('title', '代理打刻入力')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/admin/attendance_detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">代理打刻入力</h1>
    </div>

    @if (session('success'))
        <p style="color: green; text-align: center; margin: 12px 0;">{{ session('success') }}</p>
    @endif

    <div class="detail-container">
        <form action="{{ route('admin.proxy.store') }}" method="POST">
            @csrf

            <table class="detail-table">
                <tr>
                    <th>スタッフ</th>
                    <td class="value">
                        <select name="user_id" class="select-input">
                            <option value="">選択してください</option>
                            @foreach ($staffs as $staff)
                                <option value="{{ $staff->id }}" {{ old('user_id') == $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="error-container">
                                <p class="error-message">{{ $message }}</p>
                            </div>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td class="value">
                        <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}">
                        @error('date')
                            <div class="error-container">
                                <p class="error-message">{{ $message }}</p>
                            </div>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td>
                        <div class="value">
                            <input type="time" name="start_time" value="{{ old('start_time') }}">
                            <span class="symbol">～</span>
                            <input type="time" name="end_time" value="{{ old('end_time') }}">
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
                        $oldBreaks = old('breaks', [['break_start' => '', 'break_end' => '']]);
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
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tr>
                    <th>備考</th>
                    <td>
                        <div class="value">
                            <textarea name="remarks">{{ old('remarks') }}</textarea>
                        </div>
                        @error('remarks')
                            <div class="error-container">
                                <p class="error-message">{{ $message }}</p>
                            </div>
                        @enderror
                    </td>
                </tr>
            </table>

            <button type="submit" class="edit-button">登録</button>
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
        const last = allBreakRows[allBreakRows.length - 1];
        if (!last ||
            last.querySelector(".break-start").value ||
            last.querySelector(".break-end").value) {
            addNewBreakRow();
        }
    };

    document.addEventListener("input", (event) => {
        if (event.target.classList.contains("break-start") ||
            event.target.classList.contains("break-end")) {
            ensureEmptyBreakRow();
        }
    });
});
</script>
@endsection