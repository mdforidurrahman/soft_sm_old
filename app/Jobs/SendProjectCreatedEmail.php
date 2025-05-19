<?php

namespace App\Jobs;

use App\Models\Project;
use App\Mail\ProjectCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendProjectCreatedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Project $project)
    {}

    public function handle(): void
    {
        Mail::to(config('mail.admin_email'))->send(new ProjectCreated($this->project));
    }
}

// Similarly, create SendProjectStatusChangedEmail job
