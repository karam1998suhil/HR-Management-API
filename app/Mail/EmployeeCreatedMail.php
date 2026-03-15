<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Employee $employee,  // the new hire
        public Employee $manager,   // who receives the email
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Employee Added to Your Team',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.employee_created',
        );
    }
}