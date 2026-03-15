<?php

namespace App\Listeners;

use App\Events\SalaryChanged;
use App\Mail\SalaryChangedMail;
use Illuminate\Support\Facades\Mail;

class SendSalaryChangedNotifications
{
    public function handle(SalaryChanged $event): void
    {
        $employee = $event->employee;
        $old      = $event->oldSalary;
        $new      = $event->newSalary;

        // 1. Email the employee whose salary changed
        Mail::to($employee->email)->send(
            new SalaryChangedMail($employee, $old, $new)
        );

        // 2. Walk up the tree and email every manager up to founder
        $current = $employee->load('manager');

        while ($current->manager !== null) {
            $manager = $current->manager;

            Mail::to($manager->email)->send(
                new SalaryChangedMail($employee, $old, $new, $manager)
            );

            $current = $manager->load('manager');
        }
    }
}