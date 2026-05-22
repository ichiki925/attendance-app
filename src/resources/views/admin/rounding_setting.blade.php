@extends('layouts.app_admin')

@section('title','丸め処理設定')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/rounding_setting.css') }}">
@endsection

@section('content')
<div class="rounding-setting">
    <div class="header">
        <div class="vertical-line"></div>
        <h1 class="title">丸め処理設定</h1>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <p class="current-label">
        現在の設定：<span>{{ $setting->round_minutes }}分単位・{{ ['floor' => '切り捨て', 'ceil' => '切り上げ', 'round' => '四捨五入'][$setting->round_type] }}</span>
    </p>

    <form method="POST" action="{{ route('admin.rounding_setting.update') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">丸め単位（分）</label>
            <div class="radio-group">
                @foreach([1, 5, 10, 15, 30] as $min)
                    <label class="radio-label">
                        <input type="radio" name="round_minutes" value="{{ $min }}"
                            {{ $setting->round_minutes == $min ? 'checked' : '' }}>
                        {{ $min }}分
                    </label>
                @endforeach
                <label class="radio-label">
                    <input type="radio" name="round_minutes" value="custom"
                        id="customRadio"
                        {{ !in_array($setting->round_minutes, [1,5,10,15,30]) ? 'checked' : '' }}>
                    自由入力：
                    <input type="number" name="round_minutes_custom" id="customInput"
                        min="1" max="60"
                        value="{{ !in_array($setting->round_minutes, [1,5,10,15,30]) ? $setting->round_minutes : '' }}"
                        class="custom-input">分
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">丸め方向</label>
            <div class="radio-group">
                <label class="radio-label">
                    <input type="radio" name="round_type" value="floor"
                        {{ $setting->round_type === 'floor' ? 'checked' : '' }}>
                    切り捨て
                </label>
                <label class="radio-label">
                    <input type="radio" name="round_type" value="ceil"
                        {{ $setting->round_type === 'ceil' ? 'checked' : '' }}>
                    切り上げ
                </label>
                <label class="radio-label">
                    <input type="radio" name="round_type" value="round"
                        {{ $setting->round_type === 'round' ? 'checked' : '' }}>
                    四捨五入
                </label>
            </div>
        </div>

        <button type="submit" class="btn-submit">設定を保存</button>
    </form>
</div>

<script>
    // 自由入力ラジオを選んだときcustomInputにフォーカス
    document.querySelectorAll('input[name="round_minutes"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value === 'custom') {
                document.getElementById('customInput').focus();
            }
        });
    });

    // カスタム入力欄をクリックしたらcustomRadioを選択
    document.getElementById('customInput').addEventListener('focus', function() {
        document.getElementById('customRadio').checked = true;
    });

    // フォーム送信時にcustomの場合はround_minutesをcustomInputの値で上書き
    document.querySelector('form').addEventListener('submit', function(e) {
        const selected = document.querySelector('input[name="round_minutes"]:checked');
        if (selected && selected.value === 'custom') {
            const customVal = document.getElementById('customInput').value;
            selected.value = customVal;
        }
    });
</script>
@endsection