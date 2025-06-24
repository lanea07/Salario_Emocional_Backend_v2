<?php

namespace App\Services;

use App\Enums\HttpStatusCodes;
use App\Facades\ApiResponse;
use App\Mail\NewUserCreated;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class UserService {

    /**
     * Return all users
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers(): Collection {
        return User::with(['dependency', 'parent', 'positions', 'roles'])->orderBy('name')->get();
    }

    /**
     * Store a new user
     * 
     * @param array $userData
     * @return \App\Models\User
     */
    public function saveUser(array $userData): User {
        $created = DB::transaction(function () use ($userData) {
            if (!$userData['password']) {
                $password = Str::password(10, true, true, false, false);
                $userData['password'] = $password;
            }
            $rolesToAsign = array_filter($userData['rolesFormGroup'], function ($role) {
                return $role === true;
            });
            $rolesToAsign = array_keys($rolesToAsign);
            $rolesToAsign = Role::whereIn('name', $rolesToAsign)->get();
            $userData['requirePassChange'] = true;
            $user = User::create($userData);
            $user->roles()->sync($rolesToAsign);
            $data = [
                $user,
                $password
            ];
            Mail::to($user->email)->queue(new NewUserCreated($data));
            return $user;
        });
        return $created;
    }

    /**
     * Return a user by id
     * 
     * @param User $user
     * @return \App\Models\User
     */
    public function getUserById(User $user) {
        $users = User::with(['dependency', 'parent', 'positions', 'roles'])->treeOf($user, 1)->get();
        return $users->toTree();
    }

    /**
     * Update a user
     * 
     * @param array $userData
     * @param User $user
     * @return \App\Models\User
     */
    public function updateUser(array $userData, User $user): User {
        $updated = DB::transaction(function () use ($userData, $user) {
            if (!$userData['password']) {
                $userData['password'] = $user->password;
            }
            $rolesToAsign = array_filter($userData['rolesFormGroup'], function ($role) {
                return $role === true;
            });
            $rolesToAsign = array_keys($rolesToAsign);
            $rolesToAsign = Role::whereIn('name', $rolesToAsign)->get();

            $positionsToAsign = Position::where('id', $userData['position_id'])->first();
            $user->update($userData);
            $user->roles()->sync($rolesToAsign);
            $user->update(['position_id' => $positionsToAsign->id]);
            return $user;
        });
        return $updated;
    }

    /**
     * Delete a user
     * 
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function deleteUser(User $user): JsonResponse {
        return ApiResponse::sendResponse(message: __('controllers/user-controller.user_deleted'), httpCode: HttpStatusCodes::FORBIDDEN_403);
    }

    /**
     * Return all users descendants
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllDescendants(): mixed {
        $user = request()->user();
        return User::where('id', '=', $user->id)
            ->with([
                'descendants' => function ($query) use ($user) {
                    $query->whereIn('id', $user->descendants->pluck('id'));
                    $query->where('valid_id', '=', true);
                }
            ])->get();
    }

    public function getDatatable() {
        $model = User::with(['dependency', 'parent', 'positions', 'roles']);
        return DataTables::of($model)->toJson();
    }
}
