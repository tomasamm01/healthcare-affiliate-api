<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\Logger;
use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateCoverageRequest;
use App\Services\CoverageService;
use Illuminate\Http\JsonResponse;

class CoverageController extends Controller
{
    public function __construct(
        private CoverageService $service
    ) {}

    public function validate(ValidateCoverageRequest $request): JsonResponse
    {
        $result = $this->service->validateCoverage(
            $request->affiliate_id,
            $request->service_code
        );

        Logger::coverage('validated', [
            'affiliate_id' => $request->affiliate_id,
            'service_code' => $request->service_code,
            'valid' => $result['valid'],
        ]);

        return response()->json($result);
    }

    public function getAffiliateCoverage(int $affiliateId): JsonResponse
    {
        $coverage = $this->service->getAffiliateCoverage($affiliateId);

        return response()->json($coverage);
    }
}
