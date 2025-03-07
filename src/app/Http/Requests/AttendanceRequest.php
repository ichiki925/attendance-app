<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'start_time' => ['required', 'date_format:H:i', 'before:end_time'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'breaks' => ['nullable', 'array'],
            'breaks.*.break_start' => [
                'nullable', 'date_format:H:i', 'before:breaks.*.break_end', 'after_or_equal:start_time', 'before_or_equal:end_time'
            ],
            'breaks.*.break_end' => [
                'nullable', 'date_format:H:i', 'after:breaks.*.break_start', 'before_or_equal:end_time'
            ],
            'remarks' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'start_time.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'start_time.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'breaks.array' => '休憩時間のデータ形式が正しくありません',
            'breaks.*.break_start.date_format' => '休憩開始時間の形式が不正です',
            'breaks.*.break_start.before' => '休憩開始時間が勤務時間外です',
            'breaks.*.break_start.after_or_equal' => '休憩開始時間が勤務時間外です',
            'breaks.*.break_start.before_or_equal' => '休憩開始時間が勤務時間外です',
            'breaks.*.break_end.date_format' => '休憩終了時間の形式が不正です',
            'breaks.*.break_end.after' => '休憩終了時間が勤務時間外です',
            'breaks.*.break_end.before_or_equal' => '休憩終了時間が勤務時間外です',
            'remarks.required' => '備考を記入してください',
        ];
    }
}
