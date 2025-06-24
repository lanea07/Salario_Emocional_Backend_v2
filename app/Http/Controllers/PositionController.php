<?php

namespace App\Http\Controllers;

use App\Facades\ApiResponse;
use App\Services\PositionService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePositionRequest;
use App\Models\Position;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller {

    public function __construct(private PositionService $positionService) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse {
        $data = $this->positionService->getAllPositions();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreatePositionRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePositionRequest $request): JsonResponse {
        $createdPosition = $this->positionService->savePosition($request->validated());
        return ApiResponse::sendResponse($createdPosition);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Position $position
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Position $position): JsonResponse {
        $data = $this->positionService->getPositionByID($position);
        return ApiResponse::sendResponse($data);
    }

    public function update(CreatePositionRequest $request, Position $position): JsonResponse {
        $updatedPosition = $this->positionService->updatePosition($request->validated(), $position);
        return ApiResponse::sendResponse($updatedPosition);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Position $position): JsonResponse {
        $this->positionService->deletePosition($position);
        return ApiResponse::sendResponse(__('controllers/position-controller.position_deleted'), resetJWT: true);
    }

    /**
     * Return all dependencies in a datatable formmated response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function datatable() {
        $data = $this->positionService->getDatatable();
        return ApiResponse::sendResponse($data);
    }
}
