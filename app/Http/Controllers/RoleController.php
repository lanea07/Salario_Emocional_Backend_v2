<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use App\Services\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use Illuminate\Http\JsonResponse;
use \Spatie\Permission\Models\Role;

class RoleController extends Controller {

    // TODO: This controller must create spatie roles, not native app roles
    public function __construct(private RoleService $roleService) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse {
        $data = $this->roleService->getAllRoles();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateRoleRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRoleRequest $request): JsonResponse {
        $createdRole = $this->roleService->saveRole($request->validated());
        return ApiResponse::sendResponse($createdRole);
    }

    /**
     * Display the specified resource.
     *
     * @param  Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Role $role): JsonResponse {
        $data = $this->roleService->getRoleById($role);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CreateRoleRequest  $request
     * @param  Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateRoleRequest $request, Role $role): JsonResponse {
        $updatedRole = $this->roleService->updateRole($request->validated(), $role);
        return ApiResponse::sendResponse($updatedRole);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role): JsonResponse {
        $this->roleService->deleteRole($role);
        return ApiResponse::sendResponse(message: __('controllers/role-controller.role_deleted'), httpCode: HttpStatusCodes::FORBIDDEN_403);
    }

    public function datatable() {
        $data = $this->roleService->getDatatable();
        return ApiResponse::sendResponse($data);
    }
}
