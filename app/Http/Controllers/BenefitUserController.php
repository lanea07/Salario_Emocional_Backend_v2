<?php

namespace App\Http\Controllers;

use App\Framework\Enums\HttpStatusCodes;
use App\Framework\Facades\ApiResponse;
use App\Services\BenefitUserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBenefitUserRequest;
use App\Models\BenefitUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BenefitUserController extends Controller
{

    public function __construct(private BenefitUserService $benefitUserService) {}

    /**
     * Return all benefit users
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->userId;
        $year = $request->year;
        $data = $this->benefitUserService->getAllBenefitUser($userId, $year);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a new benefit user
     * 
     * @param \App\Http\Requests\CreateBenefitUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateBenefitUserRequest $request): JsonResponse
    {
        $createdBenefitUser = $this->benefitUserService->saveBenefitUser($request->validated());
        return ApiResponse::success(data: $createdBenefitUser, httpCode: HttpStatusCodes::CREATED_201, resetJWT: true);
    }

    /**
     * Return a benefit user by ID
     * 
     * @param \App\Models\BenefitUser $benefituser
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(BenefitUser $benefituser): JsonResponse
    {
        $data = $this->benefitUserService->getBenefitUserByID($benefituser);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Update a benefit user
     * 
     * @param \App\Http\Requests\CreateBenefitUserRequest $request
     * @param \App\Models\BenefitUser $benefituser
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateBenefitUserRequest $request, BenefitUser $benefituser): JsonResponse
    {
        $updatedBenefitUser = $this->benefitUserService->updateBenefitUser($request->validated(), $benefituser);
        return ApiResponse::sendResponse($updatedBenefitUser);
    }

    /**
     * Delete a benefit user
     * 
     * @param \App\Models\BenefitUser $benefituser
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(BenefitUser $benefituser): JsonResponse
    {
        $this->benefitUserService->deleteBenefitUser($benefituser);
        return ApiResponse::sendResponse(__('controllers/benefit-user-controller.benefit_user_deleted'), resetJWT: true);
    }

    /**
     * Generates a mail with user benefits data
     * 
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function exportDetail(Request $request): void
    {
        $this->benefitUserService->exportBenefits($request);
    }

    /**
     * Return all users benefits non approved
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexNonApproved(Request $request): JsonResponse
    {
        $userId = $request->userId;
        $data = $this->benefitUserService->getAllBenefitUserNonApproved($userId);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Return all benefit collaborators non approved
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexCollaboratorsNonApproved()
    {
        $data = $this->benefitUserService->getAllBenefitCollaboratorsNonApproved(request());
        return ApiResponse::sendResponse($data);
    }

    /**
     * Return all benefit collaborators
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexCollaborators(): JsonResponse
    {
        $data = $this->benefitUserService->getAllBenefitCollaborators(request());
        return ApiResponse::sendResponse($data);
    }

    /**
     * Applies a decision to a benefit user
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function decideBenefitUser(Request $request): JsonResponse
    {
        $benefitUser = BenefitUser::find($request->data['id']);
        $result = $this->benefitUserService->decideBenefitUser($request->cmd, $request->decision_comment, $benefitUser);
        return ApiResponse::sendResponse($result);
    }

    public function showByUserID(User $user, int $year): JsonResponse
    {
        $data = $this->benefitUserService->getBenefitUserByUserID($user, $year);
        return ApiResponse::sendResponse($data);
    }
}
