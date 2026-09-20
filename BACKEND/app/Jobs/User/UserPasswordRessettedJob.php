<?php

namespace App\Jobs\User;

use App\Mail\User\UserPasswordRessetedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class UserPasswordRessettedJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mailable = (new UserPasswordRessetedMail($this->data));
        Mail::to($this->data['email'])->cc('phpsup@trhtz.com')->send($mailable);
    }
}
