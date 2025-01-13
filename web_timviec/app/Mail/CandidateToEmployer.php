<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidateToEmployer extends Mailable
{
    use Queueable, SerializesModels;
    public $candidatename;
    public $message;
    public $candidateEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($candidatename,$message,$candidateEmail)
    {
        $this->candidatename=$candidatename;
        $this->message=$message;
        $this->candidateEmail=$candidateEmail;
    }

    public function build(){
        $title="Message from {$this->candidatename},{$this->candidateEmail}";
        $body=$this->message;

        return $this->subject($title)
                ->html($body);
    }
    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    // public function envelope()
    // {
    //     return new Envelope(
    //         subject: 'Candidate To Employer',
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
