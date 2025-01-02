<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusMail extends Mailable
{
    use Queueable, SerializesModels;
    public $status;
    public $jobTitle;
    public $applicantName;
    public $interviewDate;
    public $companyname;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($status, $jobTitle, $applicantName,$companyname,$interviewDate=null)
    {
        //
        $this->status = $status;
        $this->jobTitle = $jobTitle;
        $this->applicantName = $applicantName;
        $this->companyname=$companyname;
        $this->interviewDate=$interviewDate;
    }
    public function build()
    {
        
        $subject = 'Application Status Update';
        $messageContent = $this->status == 'accepted'
            ? "Dear {$this->applicantName},\n\nWe are pleased to inform you that your application for the position of {$this->jobTitle} has been accepted. Please attend the interview on {$this->interviewDate}.\n\nBest regards, {$this->companyname}"
            : "Dear {$this->applicantName},\n\nWe regret to inform you that your application for the position of {$this->jobTitle} has been rejected.\n\nBest regards, {$this->companyname}";
        
        return $this->subject($subject)
                ->html($messageContent);  // Use the html() method for sending HTML content
        
    }
    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    // public function envelope()
    // {
    //     return new Envelope(
    //         subject: 'Application Status Mail',
    //     );
    // }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    // public function content()
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    // public function attachments()
    // {
    //     return [];
    // }
}
