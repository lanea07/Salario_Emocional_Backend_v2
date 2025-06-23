<?php

namespace App\Http\Controllers;

use App\Services\PreferencesService;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreferencesController extends Controller
{

    public function __construct(private PreferencesService $preferencesService)
    {
    }

    /**
     * Returns all available preferences for User model
     * 
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->preferencesService->getAllAvailablePreferences(), 200);
    }

    /**
     * Returns all preferences for the authenticated user
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function show(User $user): JsonResponse
    {
        return response()->json($this->preferencesService->userPreferences($user), 200);
    }

    /**
     * Store a set of settings for a user
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(User $user): JsonResponse
    {
        try {
            $request = request();
            return response()->json($this->preferencesService->savePreferences($user, $request->all()), 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al guardar las preferencias'], 500);
        }
    }
}
