<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\StorePlanRequest;
use App\Http\Requests\Plan\UpdatePlanRequest;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PlanController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'plans:' . md5(json_encode($request->only(['active_only', 'per_page'])));

        $result = Cache::remember($cacheKey, now()->addHours(24), function () use ($request) {
            $query = Plan::withCount('affiliates');

            if ($request->has('active_only')) {
                $query->active();
            }

            $plans = $query->paginate($request->per_page ?? 15);

            return [
                'data' => PlanResource::collection($plans),
                'meta' => [
                    'current_page' => $plans->currentPage(),
                    'per_page' => $plans->perPage(),
                    'total' => $plans->total(),
                    'last_page' => $plans->lastPage(),
                ],
            ];
        });

        return response()->json($result);
    }

    public function store(StorePlanRequest $request): JsonResponse
    {
        $plan = Plan::create($request->validated());

        Cache::forget('plans:*');

        return (new PlanResource($plan))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Plan $plan): JsonResponse
    {
        $plan->load('affiliates');

        return (new PlanResource($plan))
            ->response();
    }

    public function update(UpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        $plan->update($request->validated());

        Cache::forget('plans:*');

        return (new PlanResource($plan))
            ->response();
    }

    public function destroy(Plan $plan): JsonResponse
    {
        if ($plan->affiliates()->exists()) {
            return response()->json([
                'error' => 'Cannot delete plan with active affiliates',
            ], 400);
        }

        $plan->delete();

        Cache::forget('plans:*');

        return response()->json(null, 204);
    }
}
