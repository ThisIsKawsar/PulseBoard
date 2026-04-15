<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectReadinessResource extends JsonResource
{
    public function toArray($request): array
    {
        $totalChecks = $this->deployment_checks_count ?? $this->deploymentChecks()->count();
        $completedChecks = $this->completed_checks_count ?? $this->deploymentChecks()->where('is_completed', true)->count();

        return [
            'project' => $this->name,
            'total_checks' => $totalChecks,
            'completed_checks' => $completedChecks,
            'is_ready_for_deployment' => $totalChecks === $completedChecks,
        ];
    }
}
