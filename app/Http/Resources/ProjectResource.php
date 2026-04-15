<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'owner_email' => $this->owner_email,
            'release_date' => optional($this->release_date)?->toDateString(),
            'total_checks' => $this->when(isset($this->deployment_checks_count), $this->deployment_checks_count, $this->deploymentChecks()->count()),
            'completed_checks' => $this->when(isset($this->completed_checks_count), $this->completed_checks_count, $this->deploymentChecks()->where('is_completed', true)->count()),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
