<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller {

    public function __construct(private UserService $userService) {
    }

    /**
     * Return all users
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse {
        $data = $this->userService->getAllUsers();
        return ApiResponse::sendResponse($data);
    }

    /**
     * Store a new user
     * 
     * @param \App\Http\Requests\CreateUserRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse {
        try {
            $createdUser = $this->userService->saveUser($request->validated());
            return ApiResponse::sendResponse(data: $createdUser, httpCode: HttpStatusCodes::CREATED_201);
        } catch (\Illuminate\Database\QueryException $th) {
            switch ($th->errorInfo[1]) {
                case 1062:
                    return ApiResponse::sendResponse(message: 'No se puede guardar el usuario porque ya existe un usuario con el mismo correo registrado.', httpCode: HttpStatusCodes::BAD_REQUEST_400);
                    break;
                case 4025:
                    return ApiResponse::sendResponse(message: $th->errorInfo[2], httpCode: HttpStatusCodes::BAD_REQUEST_400);
                    break;
                case 1:
                    return ApiResponse::sendResponse(message: $th->errorInfo[2], httpCode: HttpStatusCodes::BAD_REQUEST_400);
                    break;
                default:
                    return ApiResponse::sendResponse(message: 'Ha ocurrido un error interno, contacte con el administrador', httpCode: HttpStatusCodes::BAD_REQUEST_400);
                    break;
            }
        }
    }

    /**
     * Return a user by ID
     * 
     * @param \App\Models\User $user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user): JsonResponse {
        $data = $this->userService->getUserById($user);
        return ApiResponse::sendResponse($data);
    }

    /**
     * Update a user
     * 
     * @param \App\Http\Requests\CreateUserRequest $request
     * @param \App\Models\User $user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateUserRequest $request, User $user): JsonResponse {
        try {
            $updatedUser = $this->userService->updateUser($request->validated(), $user);
            return ApiResponse::sendResponse($updatedUser);
        } catch (\Illuminate\Database\QueryException $th) {
            switch ($th->errorInfo[1]) {
                case 1062:
                    return ApiResponse::sendResponse(message: 'No se puede actualizar el usuario porque ya existe un usuario con el mismo correo registrado.', httpCode: HttpStatusCodes::BAD_REQUEST_400);
                    break;
                case 4025:
                    return ApiResponse::sendResponse(message: $th->errorInfo[2], httpCode: HttpStatusCodes::BAD_REQUEST_400);
                    break;
                case 1:
                    return ApiResponse::sendResponse(message: $th->errorInfo[2], httpCode: HttpStatusCodes::BAD_REQUEST_400);
                    break;
                default:
                    return ApiResponse::sendResponse(message: 'Ha ocurrido un error interno, contacte con el administrador', httpCode: HttpStatusCodes::BAD_REQUEST_400);
                    break;
            }
        }
    }

    /**
     * Delete a user
     * 
     * @param \App\Models\User $user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user): JsonResponse {
        $this->userService->deleteUser($user);
        return ApiResponse::sendResponse(message: __('controllers/user-controller.user_deleted'), httpCode: HttpStatusCodes::FORBIDDEN_403);
    }

    /**
     * Return all descendants of a user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexDescendants(): JsonResponse {
        $data = $this->userService->getAllDescendants();
        return ApiResponse::sendResponse($data);
    }

    public function datatable(): JsonResponse {
        $data = $this->userService->getDatatable();
        return ApiResponse::sendResponse($data);
    }
}
