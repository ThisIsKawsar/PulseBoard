<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DeploymentCheckResource;
use App\Models\DeploymentCheck;
use Illuminate\Http\JsonResponse;

class DeploymentCheckController extends Controller
{
    public function complete(DeploymentCheck $check): JsonResponse
    {
        if ($check->is_completed) {
            return response()->json([
                'message' => 'Deployment check is already completed.',
            ], 422);
        }

        $check->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);

        return response()->json(['data' => new DeploymentCheckResource($check)]);
    }
}
