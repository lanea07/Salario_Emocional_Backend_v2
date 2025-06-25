<?php

namespace App\Services;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermissionService {

    /**
     * Get all permissions
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllPermissions(): Collection {
        return Permission::all();
    }

    /**
     * Save a new permission
     *
     * @param  array  $permissionData
     * @return Permission
     */
    public function savePermission(array $permissionData): Permission {
        $created = DB::transaction(function () use ($permissionData) {
            return Permission::create($permissionData);
        });
        return $created;
    }

    /**
     * Get a permission by ID
     *
     * @param  Permission $permission
     * @return Permission
     */
    public function getPermissionById(Permission $permission): Permission {
        return $permission;
    }

    /**
     * Update a permission
     *
     * @param array  $permissionData
     * @param Spatie\Permission\Models\Permission $permission
     * @return Spatie\Permission\Models\Permission $permission
     */
    public function updatePermission(array $permissionData, Permission $permission): Permission {
        $updated = DB::transaction(function () use ($permissionData, $permission) {
            return tap($permission)->update($permissionData);
        });
        return $updated;
    }

    /**
     * Delete a permission
     *
     * @param  Permission $permission
     * @return JsonResponse
     * @throws \Exception
     */
    public function deletePermission(Permission $permission): JsonResponse {
        return ApiResponse::sendResponse(message: __('controllers/permission-controller.permission_deleted'), httpCode: HttpStatusCodes::FORBIDDEN_403);
    }

    public function getDatatable() {
        $model = Permission::query();
        return DataTables::of($model)->toJson()->getData();
    }
}
