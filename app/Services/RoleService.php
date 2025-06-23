<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RoleService
{

    /**
     * Get all roles
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    /**
     * Save a new role
     *
     * @param  array  $roleData
     * @return Role
     */
    public function saveRole(array $roleData): Role
    {
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
    public function getRoleById(Role $role): Role
    {
        return $role;
    }

    /**
     * Update a role
     *
     * @param  array  $roleData
     * @param  Role $role
     * @return Role
     */
    public function updateRole(array $roleData, Role $role): Role
    {
        $updated = DB::transaction(function () use ($roleData, $role) {
            return tap($role)->update($roleData);
        });
        return $updated;
    }

    /**
     * Delete a role
     *
     * @param  Role $role
     * @return void
     * @throws \Exception
     */
    public function deleteRole(Role $role): void
    {
        throw new \Exception('No se puede eliminar un rol');
    }

    public function getDatatable()
    {
        $model = Role::query();
        return DataTables::of($model)->toJson();
    }
}
