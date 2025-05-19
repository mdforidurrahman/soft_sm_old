<?php

namespace App\Jobs;

use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendProjectStatusChangedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }


    /**
     * Execute the job.
     */
    public function handle()
    {
        $adminEmail = config('mail.admin_email');
        Mail::to($adminEmail)->send(new ProjectStatusChanged($this->project));
    }
}
