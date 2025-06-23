<?php

namespace App\Services;

use App\Models\Dependency;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class DependencyService
{

    /**
     * Get all dependencies
     *
     * @return Collection
     */
    public function getAllDependencies(): Collection
    {
        $userDependency = auth()->user()->dependency;
        if (auth()->user()->isAdmin()) {
            if ($userDependency->parent_id !== null) {
                $userDependency = $userDependency->rootAncestor()->first();
            }
        }
        $userDependency = $userDependency->descendantsAndSelf()->with(['users.positions'])->get();
        return $userDependency->toTree();
    }

    /**
     * Get all dependencies ancestors
     *
     * @param Request $request
     * @return Collection
     */
    public function getAllDependenciesAncestors(Request $request): Collection
    {
        $userDependency = Dependency::find($request->id)->ancestorsAndSelf()->with(['users.positions'])->get();
        return $userDependency->toTree();
    }

    /**
     * Save a new dependency
     *
     * @param array $dependencyData
     * @return Dependency
     */
    public function saveDependency(array $dependencyData): Dependency
    {
        $created = DB::transaction(function () use ($dependencyData) {
            return Dependency::create($dependencyData);
        });
        return $created;
    }

    /**
     * Get a dependency by id
     *
     * @param Dependency $dependency
     * @return Collection
     */
    public function getDependencyById(Dependency $dependency): Collection
    {
        $dependency = $dependency->descendantsAndSelf()->with(['users.positions'])->get();
        return $dependency->toTree();
    }

    /**
     * Update a dependency
     *
     * @param array $dependencyData
     * @param Dependency $dependency
     * @return Dependency
     */
    public function updateDependency(array $dependencyData, Dependency $dependency): Dependency
    {
        $updated = DB::transaction(function () use ($dependencyData, $dependency) {
            return tap($dependency)->update($dependencyData);
        });
        return $updated;
    }

    /**
     * Delete a dependency
     *
     * @param Dependency $dependency
     * @throws \Exception
     */
    public function deleteDependency(Dependency $dependency): void
    {
        throw new \Exception('No se puede eliminar una dependencia');
    }

    /**
     * Return all dependencies in flat format
     *
     * @return Collection
     */
    public function getNonTreeValidDependencies(): Collection
    {
        return Dependency::is_valid()->oldest('name')->get();
    }

    /**
     * Get datatable response format
     *
     * @return mixed
     */
    public function getDatatable()
    {
        $model = Dependency::query();
        return DataTables::of($model)->toJson();
    }
}
