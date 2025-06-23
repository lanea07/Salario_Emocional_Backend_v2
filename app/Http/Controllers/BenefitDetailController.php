<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use App\Services\BenefitDetailService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBenefitDetailRequest;
use App\Models\BenefitDetail;
use Illuminate\Http\JsonResponse;

class BenefitDetailController extends Controller {

    public function __construct(private BenefitDetailService $benefitDetailService) {
    }

    /**
     * Return all benefit details
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse {
        $data = $this->benefitDetailService->getAllBenefitDetail();
        return ApiResponse::success($data);
    }

    /**
     * Store a new benefit detail
     * 
     * @param \App\Http\Requests\CreateBenefitDetailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateBenefitDetailRequest $request): JsonResponse {
        $createdBenefitDetail = $this->benefitDetailService->saveBenefitDetail($request->name);
        return ApiResponse::success(data: $createdBenefitDetail, httpCode: HttpStatusCodes::CREATED_201, resetJWT: true);
    }

    /**
     * Return a benefit detail by ID
     * 
     * @param BenefitDetail $benefitdetail
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(BenefitDetail $benefitdetail): JsonResponse {
        $data = $this->benefitDetailService->getBenefitDetailByID($benefitdetail);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Update a benefit detail
     * 
     * @param \App\Http\Requests\CreateBenefitDetailRequest $request
     * @param BenefitDetail $benefitdetail
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateBenefitDetailRequest $request, BenefitDetail $benefitdetail): JsonResponse {
        $updatedBenefitDetail = $this->benefitDetailService->updateBenefitDetail($request->validated(), $benefitdetail);
        return ApiResponse::sendResponse($updatedBenefitDetail);
    }

    /**
     * Delete a benefit detail
     * 
     * @param BenefitDetail $benefitdetail
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BenefitDetail $benefitdetail): JsonResponse {
        $this->benefitDetailService->deleteBenefitDetail($benefitdetail);
        return ApiResponse::sendResponse(message: __('controllers/benefit-detail-controller.deleted-benefit-detail'), resetJWT: true);
    }

    /**
     * Return all benefit details in a datatable formmated response
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable() {
        return ApiResponse::sendResponse($this->benefitDetailService->getDatatable());
    }
}
