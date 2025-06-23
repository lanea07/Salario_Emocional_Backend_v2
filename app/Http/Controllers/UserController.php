<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Throwable;

class UserController extends Controller
{

    public function __construct(private UserService $userService)
    {
    }

    /**
     * Return all users
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->userService->getAllUsers(), 200);
    }

    /**
     * Store a new user
     * 
     * @param \App\Http\Requests\CreateUserRequest $request
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        try {
            return response()->json($this->userService->saveUser($request->validated()), 201);
        } catch (\Illuminate\Database\QueryException $th) {
            switch ($th->errorInfo[1]) {
                case 1062:
                    return response()->json(['message' => 'No se puede guardar el usuario porque ya existe un usuario con el mismo correo registrado.'], 400);
                    break;
                case 4025:
                    return response()->json(['message' => $th->errorInfo[2]], 400);
                    break;
                case 1:
                    return response()->json(['message' => $th->errorInfo[2]], 400);
                    break;
                default:
                    return response()->json(['message' => 'Ha ocurrido un error interno, contacte con el administrador'], 400);
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
    public function show(User $user): JsonResponse
    {
        return response()->json($this->userService->getUserById($user), 200);
    }

    /**
     * Update a user
     * 
     * @param \App\Http\Requests\CreateUserRequest $request
     * @param \App\Models\User $user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CreateUserRequest $request, User $user): JsonResponse
    {
        try {
            return response()->json($this->userService->updateUser($request->validated(), $user), 200);
        } catch (\Illuminate\Database\QueryException $th) {
            switch ($th->errorInfo[1]) {
                case 1062:
                    return response()->json(['message' => 'No se puede actualizar el usuario porque ya existe un usuario con el mismo correo registrado.'], 400);
                    break;
                case 4025:
                    return response()->json(['message' => $th->errorInfo[2]], 400);
                    break;
                case 1:
                    return response()->json(['message' => $th->errorInfo[2]], 400);
                    break;
                default:
                    return response()->json(['message' => 'Ha ocurrido un error interno, contacte con el administrador'], 400);
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
    public function destroy(User $user): JsonResponse
    {
        try {
            $this->userService->deleteUser($user);
            return response()->json(['message' => 'Usuario eliminado'], 200);
        } catch (Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Return all descendants of a user
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexDescendants(): JsonResponse
    {
        return response()->json($this->userService->getAllDescendants(), 200);
    }

    public function datatable(): JsonResponse
    {
        try {
            return response()->json($this->userService->getDatatable(), 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }
}
