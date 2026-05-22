@extends('layouts.app_admin')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin/attendance_type_index.css') }}">
@endsection

@section('content')
<div class="attendance-type-setting">
    <div class="header">
        <div class="vertical-line"></div>
        <h2 class="title">勤怠区分設定</h2>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- 区分一覧 --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>区分名</th>
                    <th>有給</th>
                    <th>休日</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($types as $type)
                <tr>
                    <td>
                        <span class="color-badge" style="background:{{ $type->color }};"></span>
                        {{ $type->name }}
                    </td>
                    <td>{{ $type->is_paid ? '✓' : '-' }}</td>
                    <td>{{ $type->is_holiday ? '✓' : '-' }}</td>
                    <td>
                        <form action="{{ route('admin.attendance_type.destroy', $type->id) }}" method="POST" onsubmit="return confirm('削除しますか？')">
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
        <form action="{{ route('admin.attendance_type.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>区分名</label>
                    <input type="text" name="name" class="form-input" required maxlength="255">
                </div>
                <div class="form-group">
                    <label>カラー</label>
                    <input type="color" name="color" class="form-input-color" value="#6c757d">
                </div>
                <div class="form-group form-check-group">
                    <label class="check-label">
                        <input type="checkbox" name="is_paid" value="1"> 有給
                    </label>
                    <label class="check-label">
                        <input type="checkbox" name="is_holiday" value="1"> 休日
                    </label>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-submit">追加</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection