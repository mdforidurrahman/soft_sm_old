<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Jobs\SendProjectCreatedEmail;
use App\Jobs\SendProjectStatusChangedEmail;
use App\Models\Project;
use App\Models\User;
use App\Services\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\DataTables;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projectService) {
    }

    public function index() {
        $projects = $this->projectService->getAllProjects();

        if (request()->ajax()) {
            return DataTables::of($projects)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return view('admin.projects.action.status', [
                        'project' => $row,
                    ]);
                })
                ->addColumn('action', function ($row) {
                    return view('components.action-buttons', [
                        'id' => $row->id,
                        'model' => 'project',
                        'editRoute' => 'projects.edit',
                        'deleteRoute' => 'projects.destroy',
                    ])->render();
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('admin.projects.index', compact('projects'));
    }

    public function create() {
        $staffMembers = User::whereRole('staff')->get();
        return view('admin.projects.create', compact('staffMembers'));
    }

    public function store(ProjectRequest $request) {
        $project = $this->projectService->createProject($request->validated());

        SendProjectCreatedEmail::dispatch($project);

        if ($request->ajax()) {
            return response()->json(['message' => 'Project created successfully', 'project' => $project]);
        }

        return redirect()->route('admin.projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project) {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(ProjectRequest $request, Project $project) {
        $oldStatus = $project->status;
        $newProject = $this->projectService->updateProject($project, $request->validated());


        if ($oldStatus !== $newProject->status) {
            SendProjectStatusChangedEmail::dispatch($project);
        }
        return redirect()->route('admin.projects.index')->with('success', 'Project updated successfully.');
    }

    public function updateStatus(Request $request, Project $project) {
        $request->validate([
            'status' => ['required', Rule::in(ProjectStatus::getValues())],
        ]);

        $oldStatus = $project->status;
        $project->update(['status' => $request->status]);

        if ($oldStatus !== $project->status) {
            SendProjectStatusChangedEmail::dispatch($project);
        }

        return response()->json(['message' => 'Status updated successfully']);
    }

    public function destroy(Project $project) {
        $this->projectService->deleteProject($project);
        return redirect()->route('admin.projects.index')->with('success', 'Project deleted successfully.');
    }

    public function recycleBin() {
        $deletedProjects = $this->projectService->getDeletedProjects();
        return view('admin.projects.recycle-bin', compact('deletedProjects'));
    }

    public function restore(Request $request) {
        $this->projectService->restoreProjects($request->input('projects', []));
        return redirect()->route('admin.projects.recycle-bin')->with('success', 'Selected projects restored successfully.');
    }
}
