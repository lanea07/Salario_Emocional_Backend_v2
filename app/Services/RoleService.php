<?php

namespace App\Services;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RoleService {

    /**
     * Get all roles
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles(): Collection {
        return Role::all();
    }

    /**
     * Save a new role
     *
     * @param  array  $roleData
     * @return Role
     */
    public function saveRole(array $roleData): Role {
        $created = DB::transaction(function () use ($roleData) {
            return Role::create($roleData);
        });
        return $created;
    }

    /**
     * Get a role by ID
     *
     * @param  Role $role
     * @return Role
     */
    public function getRoleById(Role $role): Collection {
        return $role->with(['permissions'])->get();
    }

    /**
     * Update a role
     *
     * @param array  $roleData
     * @param Spatie\Permission\Models\Role $role
     * @return Spatie\Permission\Models\Role $role
     */
    public function updateRole(array $roleData, Role $role): Role {
        $updated = DB::transaction(function () use ($roleData, $role) {
            return tap($role)->update($roleData);
        });
        return $updated;
    }

    /**
     * Delete a role
     *
     * @param  Role $role
     * @return JsonResponse
     * @throws \Exception
     */
    public function deleteRole(Role $role): JsonResponse {
        return ApiResponse::sendResponse(message: __('controllers/role-controller.role_deleted'), httpCode: HttpStatusCodes::FORBIDDEN_403);
    }

    public function getDatatable() {
        $model = Role::query();
        return DataTables::of($model)->toJson()->getData();
    }
}
