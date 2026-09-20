<?php

namespace App\Jobs\User;

use App\Mail\User\NewUserCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class NewUserCreatedJob implements ShouldQueue
{
    use Queueable;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mailable = (new NewUserCreatedMail($this->data));
        Mail::to($this->data['email'])->cc('phpsup@trhtz.com')->send($mailable);
    }
}
