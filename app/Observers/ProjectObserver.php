<?php

namespace App\Observers;

use App\Models\Project;
use App\Jobs\SendProjectCreatedEmail;
use App\Jobs\SendProjectStatusChangedEmail;

class ProjectObserver
{
    public function created(Project $project): void
    {
        SendProjectCreatedEmail::dispatch($project);
    }

    public function updated(Project $project): void
    {
        if ($project->isDirty('status')) {
            SendProjectStatusChangedEmail::dispatch($project);
        }
    }
}
