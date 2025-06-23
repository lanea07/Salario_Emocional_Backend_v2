<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Services\DependencyService;
use App\Http\Controllers\Controller;
use App\Models\Dependency;
use App\Http\Requests\StoreDependencyRequest;
use App\Http\Requests\UpdateDependencyRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DependencyController extends Controller {

    public function __construct(private DependencyService $dependencyService) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {
        $data = $this->dependencyService->getAllDependencies();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreDependencyRequest $request): JsonResponse {
        try {
            return response()->json($this->dependencyService->saveDependency($request->validated()), 201);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     * 
     * @param  \App\Models\Dependency  $dependency
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Dependency $dependency): JsonResponse {
        return response()->json($this->dependencyService->getDependencyById($dependency), 200);
    }

    /**
     * Update the specified resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Dependency  $dependency
     */
    public function update(UpdateDependencyRequest $request, Dependency $dependency): JsonResponse {
        try {
            return response()->json($this->dependencyService->updatedependency($request->validated(), $dependency), 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  \App\Models\Dependency  $dependency
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Dependency $dependency): JsonResponse {
        try {
            $this->dependencyService->deleteDependency($dependency);
            return response()->json(['message' => 'Dependencia eliminada'], 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Return all ancestors of a dependency
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAncestors(Request $request) {
        return response()->json($this->dependencyService->getAllDependenciesAncestors($request), 200);
    }

    /**
     * Return all dependencies in flat format
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNonTreeValidDependencies() {
        return response()->json($this->dependencyService->getNonTreeValidDependencies(), 200);
    }

    /**
     * Return all dependencies in a datatable formmated response
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable() {
        try {
            return response()->json($this->dependencyService->getDatatable(), 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
