<?php

namespace App\Mail;

use App\Models\AttendanceRequest as AttendanceRequestModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttendanceRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public AttendanceRequestModel $attendanceRequest;

    public function __construct(AttendanceRequestModel $attendanceRequest)
    {
        $this->attendanceRequest = $attendanceRequest;
    }

    public function build()
    {
        return $this->subject('【勤怠修正申請】' . $this->attendanceRequest->user->name . 'さんから申請が届きました')
                    ->view('emails.attendance_request');
    }
}