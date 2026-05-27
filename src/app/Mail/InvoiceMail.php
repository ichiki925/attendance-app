<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $staff;
    public $startDate;
    public $endDate;
    public $totalTime;
    public $totalAmount;
    public $pdfContent;

    public function __construct(User $staff, $startDate, $endDate, $totalTime, $totalAmount, $pdfContent)
    {
        $this->staff = $staff;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalTime = $totalTime;
        $this->totalAmount = $totalAmount;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->subject('【請求書】' . $this->startDate . ' 〜 ' . $this->endDate)
            ->view('admin.mail.invoice_mail')
            ->attachData($this->pdfContent, 'invoice.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}