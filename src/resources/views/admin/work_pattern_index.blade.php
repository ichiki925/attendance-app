@extends('layouts.app_admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/work_pattern_index.css') }}">
@endsection

@section('content')
<div class="work-pattern-setting">
    <div class="header">
        <div class="vertical-line"></div>
        <h2 class="title">勤務パターン設定</h2>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- パターン一覧 --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>パターン名</th>
                    <th>開始時刻</th>
                    <th>終了時刻</th>
                    <th>休憩時間</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($patterns as $pattern)
                <tr>
                    <td>{{ $pattern->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($pattern->start_time)->format('H:i') }}</td>
                    <td>{{ \Carbon\Carbon::parse($pattern->end_time)->format('H:i') }}</td>
                    <td>{{ $pattern->break_minutes }}分</td>
                    <td>
                        <form action="{{ route('admin.work_pattern.destroy', $pattern->id) }}" method="POST" onsubmit="return confirm('削除しますか？')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 新規追加フォーム --}}
    <div class="add-form-wrapper">
        <p class="form-title">新規追加</p>
        <form action="{{ route('admin.work_pattern.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>パターン名</label>
                    <input type="text" name="name" class="form-input" required maxlength="255">
                </div>
                <div class="form-group">
                    <label>開始時刻</label>
                    <input type="time" name="start_time" class="form-input-time" required>
                </div>
                <div class="form-group">
                    <label>終了時刻</label>
                    <input type="time" name="end_time" class="form-input-time" required>
                </div>
                <div class="form-group">
                    <label>休憩時間（分）</label>
                    <input type="number" name="break_minutes" class="form-input-number" value="60" min="0" max="480">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-submit">追加</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection