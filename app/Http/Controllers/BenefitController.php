<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use App\Services\BenefitService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBenefitRequest;
use App\Models\Benefit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BenefitController extends Controller
{

    public function __construct(private BenefitService $benefitService) {}

    /**
     * Return all benefits
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = $this->benefitService->getAllBenefits();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a new benefit
     * 
     * @param CreateBenefitRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateBenefitRequest $request): JsonResponse
    {
        $createdBenefit = $this->benefitService->saveBenefit($request->validated());
        return ApiResponse::sendResponse(data: $createdBenefit, httpCode: HttpStatusCodes::CREATED_201, resetJWT: true);
    }

    /**
     * Return a benefit by id
     * 
     * @param Benefit $benefit
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Benefit $benefit): JsonResponse
    {
        $data = $this->benefitService->getBenefitByID($benefit);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Update a benefit
     * 
     * @param CreateBenefitRequest $request
     * @param Benefit $benefit
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateBenefitRequest $request, Benefit $benefit): JsonResponse
    {
        $updatedBenefit = $this->benefitService->updateBenefit($request->validated(), $benefit);
        return ApiResponse::sendResponse(data: $updatedBenefit, httpCode: HttpStatusCodes::OK_200, resetJWT: true);
    }

    /**
     * Delete a benefit
     * 
     * @param Benefit $
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Benefit $benefit): JsonResponse
    {
        $this->benefitService->deleteBenefit($benefit);
        return ApiResponse::sendResponse(message: __('controllers/benefit-controller.deleted-benefit'), resetJWT: true);
    }

    /**
     * Return all valid benefits
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAvailable(): JsonResponse
    {
        $data = $this->benefitService->getAllEnabledBenefits();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Returns all available preferences for User model
     * 
     * @return JsonResponse
     */
    public function indexPreferences(): JsonResponse
    {
        $data = $this->benefitService->getAllAvailablePreferences();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Returns all preferences for the requested benefit
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function showPreferences(Benefit $benefit): JsonResponse
    {
        $data = $this->benefitService->benefitPreferences($benefit);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a set of settings for a benefit
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function storePreferences(Benefit $benefit): JsonResponse
    {
        $request = request();
        $allowedSettings = array_keys($benefit->settings()->allAllowed()->toArray());
        $this->benefitService->savePreferences($benefit, $request->all($allowedSettings));
        return ApiResponse::sendResponse(message: __('controllers/benefit-controller.preferences-saved'), httpCode: HttpStatusCodes::CREATED_201, resetJWT: true);
    }

    /**
     * Return all benefits in a datatable formmated response
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable(): JsonResponse
    {
        $data = $this->benefitService->getDatatable();
        return ApiResponse::sendResponse($data);
    }
}
