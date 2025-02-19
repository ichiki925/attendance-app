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
            'breaks.*.start' => ['nullable', 'date_format:H:i', 'before:breaks.*.end', 'before_or_equal:end_time'],
            'breaks.*.end' => ['nullable', 'date_format:H:i', 'after:breaks.*.start', 'before_or_equal:end_time'],
            'remarks' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'start_time.required' => '出勤時間もしくは退勤時間が不適切な値です',
            'start_time.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'end_time.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'breaks.*.start.before_or_equal' => '休憩時間が勤務時間外です',
            'breaks.*.start.date_format' => '休憩開始時間の形式が不正です',
            'breaks.*.start.before' => '休憩の開始時間は終了時間より前に設定してください',
            'breaks.*.end.before_or_equal' => '休憩時間が勤務時間外です',
            'breaks.*.end.date_format' => '休憩終了時間の形式が不正です',
            'breaks.*.end.after' => '休憩の終了時間は開始時間より後に設定してください',
            'remarks.required' => '備考を記入してください',
        ];
    }
}
