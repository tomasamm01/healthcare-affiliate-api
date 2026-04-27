<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\Affiliate\AddDependentRequest;
use App\Http\Requests\Affiliate\ChangeStatusRequest;
use App\Http\Requests\Affiliate\StoreAffiliateRequest;
use App\Http\Requests\Affiliate\UpdateAffiliateRequest;
use App\Http\Resources\AffiliateResource;
use App\Http\Resources\FamilyGroupResource;
use App\Models\Affiliate;
use App\Services\AffiliateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateController extends Controller
{
    public function __construct(
        private AffiliateService $service
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = Affiliate::with('plan');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->has('holder_only')) {
            $query->holders();
        }

        // Use cursor pagination for large datasets
        if ($request->has('cursor')) {
            $affiliates = $query->cursorPaginate($request->per_page ?? 15);

            return response()->json([
                'data' => AffiliateResource::collection($affiliates),
                'meta' => [
                    'next_cursor' => $affiliates->nextCursor()?->encode(),
                    'prev_cursor' => $affiliates->previousCursor()?->encode(),
                    'per_page' => $affiliates->perPage(),
                ],
            ]);
        }

        // Fallback to standard pagination
        $affiliates = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'data' => AffiliateResource::collection($affiliates),
            'meta' => [
                'current_page' => $affiliates->currentPage(),
                'per_page' => $affiliates->perPage(),
                'total' => $affiliates->total(),
                'last_page' => $affiliates->lastPage(),
            ],
        ]);
    }

    public function store(StoreAffiliateRequest $request): JsonResponse
    {
        $affiliate = $this->service->create($request->validated());

        Logger::affiliate('created', [
            'affiliate_id' => $affiliate->id,
            'dni' => $affiliate->dni,
            'user_id' => $request->user()->id,
        ]);

        return (new AffiliateResource($affiliate))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Affiliate $affiliate): JsonResponse
    {
        $affiliate->load('plan', 'dependents', 'holder');

        return (new AffiliateResource($affiliate))
            ->response();
    }

    public function update(UpdateAffiliateRequest $request, Affiliate $affiliate): JsonResponse
    {
        $affiliate = $this->service->update($affiliate, $request->validated());

        Logger::affiliate('updated', [
            'affiliate_id' => $affiliate->id,
            'dni' => $affiliate->dni,
            'user_id' => $request->user()->id,
        ]);

        return (new AffiliateResource($affiliate))
            ->response();
    }

    public function destroy(Affiliate $affiliate): JsonResponse
    {
        $affiliateId = $affiliate->id;
        $dni = $affiliate->dni;
        $affiliate->delete();

        Logger::affiliate('deleted', [
            'affiliate_id' => $affiliateId,
            'dni' => $dni,
        ]);

        return response()->json(null, 204);
    }

    public function changeStatus(ChangeStatusRequest $request, Affiliate $affiliate): JsonResponse
    {
        $affiliate = $this->service->changeStatus($affiliate, $request->status);

        return response()->json([
            'status' => $affiliate->status->value,
            'status_label' => $affiliate->status->label(),
        ]);
    }

    public function getStatus(Affiliate $affiliate): JsonResponse
    {
        return response()->json([
            'status' => $affiliate->status->value,
            'status_label' => $affiliate->status->label(),
            'can_validate_coverage' => $affiliate->status->canValidateCoverage(),
        ]);
    }

    public function addDependent(AddDependentRequest $request, Affiliate $holder): JsonResponse
    {
        $dependent = $this->service->addDependent($holder, $request->validated());

        return (new AffiliateResource($dependent))
            ->response()
            ->setStatusCode(201);
    }

    public function getFamilyGroup(Affiliate $holder): JsonResponse
    {
        $familyGroup = $this->service->getFamilyGroup($holder);

        return (new FamilyGroupResource($familyGroup->first()))
            ->response();
    }

    public function removeDependent(Affiliate $dependent): JsonResponse
    {
        $this->service->removeDependent($dependent);

        return response()->json(null, 204);
    }
}
