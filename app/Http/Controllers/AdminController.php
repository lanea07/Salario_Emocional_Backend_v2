<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function __construct(private AdminService $adminService)
    {
    }

    /**
     * Return all users benefits using the filters in the request
     * 
     * @param Request $request
     * @return 
     */
    public function getAllBenefitUser(Request $request): JsonResponse
    {
        $data = $this->adminService->getAllBenefits($request);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Returns users benefits grouped by benefit
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllGroupedBenefits(Request $request): JsonResponse
    {
        $data = $this->adminService->getAllGroupedBenefits($request);
        return ApiResponse::sendResponse($data);
    }
}
