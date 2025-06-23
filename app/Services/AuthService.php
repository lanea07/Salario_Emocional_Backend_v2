<?php

namespace App\Services;

use App\Mail\PasswordForgottenNewPass;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthService
{

    /**
     * Validate user login
     * 
     * @param string $email
     * @return User|null
     */
    public function validateUserLogin(string $email): User | null
    {
        $user = User::where('email', $email)->first();
        return $user;
    }

    /**
     * Log out a user
     * 
     * @return void
     */
    public function logoutUser(): void
    {
        auth()->user()->tokens()->delete();
    }

    /**
     * Change user password
     * 
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    public function validatePasswordChange(Request $request): void
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);
        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            throw ValidationException::withMessages([
                'message' => 'La contraseña actual es incorrecta'
            ]);
        }
        if (Hash::check($validated['password'], auth()->user()->password)) {
            throw ValidationException::withMessages([
                'message' => 'La nueva contraseña no puede ser igual a la anterior'
            ]);
        }
        auth()->user()->update([
            'password' => Hash::make($validated['password']),
            'requirePassChange' => false
        ]);
    }

    /**
     * Log in as another user
     * 
     * @param User $currentUser
     * @param int $loginAsUser
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function loginAs(User $currentUser, int $loginAsUser): JsonResponse
    {
        if (!$currentUser->isAdmin()) {
            throw new \Exception('No tienes permisos para realizar esta acción');
        }
        $user = User::find($loginAsUser);
        return response()->json(
            [
                'token' => $user->createToken(request()->device_name, ['*'], now()->addDay())->plainTextToken,
                'id' => $user->id,
                'user' => $user->only('name', 'email', 'id'),
                'admin' => (bool)$user->isAdmin(),
                'simulated' => true,
            ],
            200
        );
    }

    public function forgotPassword(Request $request): void
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            DB::transaction(function () use ($user) {
                $password = Str::password(10, true, true, false, false);
                $user['password'] = $password;
                $user['requirePassChange'] = true;
                $user->save();
                $data = [
                    $user,
                    $password
                ];
                Mail::to($user->email)->queue(new PasswordForgottenNewPass($data));
                return $user;
            });
        }
    }
}
