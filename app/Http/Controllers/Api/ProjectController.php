<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDeploymentCheckRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Resources\ProjectReadinessResource;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::withCount([
            'deploymentChecks',
            'deploymentChecks as completed_checks_count' => function ($query) {
                $query->where('is_completed', true);
            },
        ])
            ->latest()
            ->paginate(15);

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = Project::create($request->validated());

        return response()->json(['data' => new ProjectResource($project)], 201);
    }

    public function storeCheck(StoreDeploymentCheckRequest $request, Project $project): JsonResponse
    {
        $check = $project->deploymentChecks()->create($request->validated());

        return response()->json(['data' => $check], 201);
    }

    public function readiness(Project $project): JsonResponse
    {
        $project->loadCount([
            'deploymentChecks',
            'deploymentChecks as completed_checks_count' => function ($query) {
                $query->where('is_completed', true);
            },
        ]);

        return response()->json(['data' => new ProjectReadinessResource($project)]);
    }
}
