<?php

namespace App\Interfaces;

use App\Models\Project;

interface ProjectRepositoryInterface
{
    public function getAllProjects();
    public function getDeletedProjects();
    public function createProject(array $data): Project;
    public function updateProject(Project $project, array $data): bool;
    public function deleteProject(Project $project): bool;
    public function restoreProjects(array $ids): int;
    public function findById(int $id): ?Project;
}
