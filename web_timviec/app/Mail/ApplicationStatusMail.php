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
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($status, $jobTitle, $applicantName,$interviewDate=null)
    {
        //
        $this->status = $status;
        $this->jobTitle = $jobTitle;
        $this->applicantName = $applicantName;
        $this->interviewDate=$interviewDate;;
    }
    public function build()
    {
        return $this->subject("Your application status for $this->jobTitle")
                    ->view('emails.application_status')
                    ->with([
                        'status' => $this->status,
                        'jobTitle' => $this->jobTitle,
                        'applicantName' => $this->applicantName,
                        'interviewDate'=>$this->interviewDate,
                    ]);
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
