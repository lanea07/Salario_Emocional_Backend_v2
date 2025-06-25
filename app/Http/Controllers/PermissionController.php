<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use App\Services\PermissionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\JsonResponse;
use \Spatie\Permission\Models\Permission;

class PermissionController extends Controller {

    public function __construct(private PermissionService $permissionService) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse {
        $data = $this->permissionService->getAllPermissions();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorePermissionRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePermissionRequest $request): JsonResponse {
        $createdPermission = $this->permissionService->savePermission($request->validated());
        return ApiResponse::sendResponse($createdPermission);
    }

    /**
     * Display the specified resource.
     *
     * @param  Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Permission $permission): JsonResponse {
        $data = $this->permissionService->getPermissionById($permission);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\StorePermissionRequest  $request
     * @param  Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePermissionRequest $request, Permission $permission): JsonResponse {
        $updatedPermission = $this->permissionService->updatePermission($request->validated(), $permission);
        return ApiResponse::sendResponse($updatedPermission);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Permission $permission): JsonResponse {
        $this->permissionService->deletePermission($permission);
        return ApiResponse::sendResponse(message: __('controllers/permission-controller.permission_deleted'), httpCode: HttpStatusCodes::FORBIDDEN_403);
    }

    public function datatable() {
        $data = $this->permissionService->getDatatable();
        return ApiResponse::sendResponse($data);
    }
}
