<?php

namespace App\Services;

use App\Interfaces\ProjectRepositoryInterface;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class ProjectService
{
    private $projectRepository;

    public function __construct(ProjectRepositoryInterface $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function getAllProjects()
    {
        return $this->projectRepository->getAllProjects();
    }

    public function getDeletedProjects()
    {
        return $this->projectRepository->getDeletedProjects();
    }


    public function createProject(array $data)
    {
        if (isset($data['file']) && is_array($data['file'])) {
            $data['file_paths'] = $this->uploadFiles($data['file']);
        }

        return $this->projectRepository->createProject($data);
    }



    public function updateProject(Project $project, array $data)
    {
        if (isset($data['file'])) {
            $this->deleteOldFile($project->file_path);
            $data['file_path'] = $this->uploadFiles($data['file']);
        }

        return $this->projectRepository->updateProject($project, $data);
    }

    public function deleteProject(Project $project)
    {
        return $this->projectRepository->deleteProject($project);
    }

    public function restoreProjects(array $ids)
    {
        return $this->projectRepository->restoreProjects($ids);
    }

    private function uploadFiles($files)
    {
        $filePaths = [];
        foreach ($files as $file) {
            $filePaths[] = Storage::disk('public')->put('project_files', $file);
        }
        return $filePaths;
    }
    private function deleteOldFile($filePath): void
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }
}
