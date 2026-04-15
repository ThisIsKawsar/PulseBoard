<?php

use App\Models\Project;

it('returns deployment readiness for a project', function () {
    $project = Project::create([
        'name' => 'PulseBoard',
        'owner_email' => 'owner@example.com',
    ]);

    $project->deploymentChecks()->createMany([
        ['title' => 'Database migration reviewed'],
        ['title' => 'Security audit completed'],
    ]);

    $project->deploymentChecks()->first()->update([
        'is_completed' => true,
        'completed_at' => now(),
    ]);

    $this->getJson("/api/projects/{$project->id}/readiness")
        ->assertStatus(200)
        ->assertJson([
            'data' => [
                'project' => 'PulseBoard',
                'total_checks' => 2,
                'completed_checks' => 1,
                'is_ready_for_deployment' => false,
            ],
        ]);
});
