<?php

namespace App\Http\Controllers;

use App\Framework\Facades\ApiResponse;
use App\Services\DependencyService;
use App\Http\Controllers\Controller;
use App\Models\Dependency;
use App\Http\Requests\StoreDependencyRequest;
use App\Http\Requests\UpdateDependencyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DependencyController extends Controller
{

    public function __construct(private DependencyService $dependencyService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->dependencyService->getAllDependencies();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreDependencyRequest $request): JsonResponse
    {
        $createdDependency = $this->dependencyService->saveDependency($request->validated());
        return ApiResponse::sendResponse($createdDependency);
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Dependency $dependency): JsonResponse
    {
        $data = $this->dependencyService->getDependencyById($dependency);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dependency  $dependency
     */
    public function update(UpdateDependencyRequest $request, Dependency $dependency): JsonResponse
    {
        $updatedDependency = $this->dependencyService->updatedependency($request->validated(), $dependency);
        return ApiResponse::sendResponse($updatedDependency);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Models\Dependency  $dependency
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Dependency $dependency): JsonResponse
    {
        $this->dependencyService->deleteDependency($dependency);
        return ApiResponse::sendResponse(__('controllers/dependency-controller.dependency_deleted'), resetJWT: true);
    }

    /**
     * Return all ancestors of a dependency
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAncestors(Request $request)
    {
        $data = $this->dependencyService->getAllDependenciesAncestors($request);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Return all dependencies in flat format
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNonTreeValidDependencies()
    {
        $data = $this->dependencyService->getNonTreeValidDependencies();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Return all dependencies in a datatable formmated response
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable()
    {
        $data = $this->dependencyService->getDatatable();
        return ApiResponse::sendResponse($data);
    }
}
