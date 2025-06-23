<?php

namespace App\Http\Controllers;

use App\Services\RoleService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Throwable;

class RoleController extends Controller
{

    public function __construct(private RoleService $roleService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->roleService->getAllRoles(), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateRoleRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateRoleRequest $request): JsonResponse
    {
        try {
            return response()->json($role = $this->roleService->saveRole($request->validated()), 201);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Role $role): JsonResponse
    {
        return response()->json($this->roleService->getRoleById($role), 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CreateRoleRequest  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateRoleRequest $request, Role $role): JsonResponse
    {
        try {
            return response()->json($role = $this->roleService->updateRole($request->validated(), $role), 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Role $role): JsonResponse
    {
        try {
            $this->roleService->deleteRole($role);
            return response()->json(['message' => 'Rol eliminado'], 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function datatable()
    {
        try {
            return response()->json($this->roleService->getDatatable(), 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
