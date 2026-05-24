<?php

namespace App\Mail;

use App\Models\AttendanceRequest as AttendanceRequestModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AttendanceApprovedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public AttendanceRequestModel $attendanceRequest;

    public function __construct(AttendanceRequestModel $attendanceRequest)
    {
        $this->attendanceRequest = $attendanceRequest;
    }

    public function build()
    {
        return $this->subject('【勤怠修正承認】勤怠修正申請が承認されました')
                    ->view('emails.attendance_approved');
    }
}