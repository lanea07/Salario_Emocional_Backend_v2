<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Services\PreferencesService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreferencesController extends Controller {

    public function __construct(private PreferencesService $preferencesService) {
    }

    /**
     * Returns all available preferences for User model
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        $data = $this->preferencesService->getAllAvailablePreferences();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Returns all preferences for the authenticated user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse {
        $data = $this->preferencesService->userPreferences($user);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a set of settings for a user
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(User $user): JsonResponse {
        $request = request();
        $createdPreference = $this->preferencesService->savePreferences($user, $request->all());
        return ApiResponse::sendResponse($createdPreference);
    }
}
