<?php

namespace App\Repositories;

use App\Interfaces\ProjectRepositoryInterface;
use App\Models\Project;

class ProjectRepository implements ProjectRepositoryInterface
{
    protected $model;

    public function __construct(Project $project) {
        $this->model = $project;
    }

    public function getAllProjects() {
        return $this->model->with('staff');
    }

    public function getDeletedProjects() {
        return $this->model->onlyTrashed()->with('staff');
    }

    public function createProject(array $data): Project {
        return $this->model->create($data);
    }

    public function updateProject(Project $project, array $data): bool {
        return $project->update($data);
    }

    public function deleteProject(Project $project): bool {
        return $project->delete();
    }

    public function restoreProjects(array $ids): int {
        return $this->model->onlyTrashed()->whereIn('id', $ids)->restore();
    }

    public function findById(int $id): ?Project {
        return $this->model->findOrFail($id);
    }
}
