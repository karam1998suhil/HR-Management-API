<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SalaryChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Employee $employee,   // whose salary changed
        public float    $oldSalary,
        public float    $newSalary,
        public ?Employee $notifiedPerson = null, // null = notify the employee himself
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Salary Update Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.salary_changed',
        );
    }
}