@extends('layouts.app_admin')
@section('title', 'スタッフ一覧')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff_list.css') }}">
@endsection
@section('content')
<div class="staff_list">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">スタッフ一覧</h1>
    </div>
    @if(session('success'))
        <div style="background:#d4edda;color:#155724;padding:12px 20px;border-radius:6px;margin-bottom:16px;border:1px solid #c3e6cb;">{{ session('success') }}
    </div>
    @endif
    <div class="table-container">
        <table class="staff-table">
            <thead>
                <tr>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>時給（円）</th>
                    <th>月次勤怠</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($staffs as $staff)
                <tr>
                    <td>{{ $staff->name }}</td>
                    <td>{{ $staff->email }}</td>
                    <td>
                        <form action="{{ route('admin.staff.updateWage', $staff->id) }}" method="POST" style="display:flex;gap:8px;align-items:center;justify-content:flex-end;">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="hourly_wage" value="{{ $staff->hourly_wage }}" min="0" style="width:90px;padding:4px;">
                            <button type="submit" class="wage-btn">更新</button>
                        </form>
                    </td>
                    <td><a href="{{ route('admin.staff.attendance.list', ['id' => $staff->id]) }}" class="detail-link">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection