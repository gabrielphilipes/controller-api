<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Exceptions\TokenExpiredException;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Exceptions\LogoutException;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginAuthRequest;
use App\Exceptions\LoginInvalidException;
use App\Exceptions\ModelNotFoundException;
use App\Http\Requests\RegisterAuthRequest;
use App\Models\PasswordReset;
use Throwable;

class AuthController extends Controller
{
    const RESET_PASSWORD_TOKEN_EXPIRED = 60; // Minutes

    /**
     * @param RegisterAuthRequest $request
     * @return UserResource
     */
    public function register(RegisterAuthRequest $request): UserResource
    {
        $input = $request->validated();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // TODO: Execute job to send email

        return UserResource::make($user);
    }

    /**
     * @param LoginAuthRequest $request
     * @return UserResource
     * @throws LoginInvalidException
     */
    public function login(LoginAuthRequest $request): UserResource
    {
        $input = $request->validated();

        /** @var User $user */
        $user = User::where('email', $input['email'])->first();
        if (!$user || !Hash::check($input['password'], $user->password)) {
            throw new LoginInvalidException();
        }

        /**
         * Delete all tokens
         * By removing all tokens, we ensure that the user can only have one session at a time.
         * If you want to allow multiple sessions, you can comment out this line.
         */
        $user->tokens()->delete();

        $device = $input['device_name'] ?? $request->userAgent(); // Create a custom device name
        $expiredTime = Carbon::now()->addMinutes(config('sanctum.expiration'));
        $abilities = $user->permissions;

        $token = $user->createToken($device, $abilities, $expiredTime)->plainTextToken;

        return UserResource::make($user)->additional([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expired_at' => $expiredTime->toDateTimeString(),
        ]);
    }

    /**
     * Execute logout
     *
     * @return void
     * @throws LogoutException
     */
    public function logout(): void
    {
        try {
            /** @var User $user */
            $user = auth()->user();
            $user->currentAccessToken()->delete();
        } catch (Throwable $th) {
            throw new LogoutException('Error in logout: ' . $th->getMessage());
        }
    }

    /**
     * @return JsonResponse
     */
    public function increaseTokenLifetime(): JsonResponse
    {
        try {
            /** @var User $user */
            $user = auth()->user();

            $token = $user->currentAccessToken();
            $token->expires_at = Carbon::now()->addMinutes(config('sanctum.expiration'));
            $token->save();

            return response()->json([
                'access_token' => request()->bearerToken(),
                'token_type' => 'Bearer',
                'expired_at' => $token->expires_at->toDateTimeString(),
            ]);
        } catch (Throwable $th) {
            return response()->json([
                'error'   => 'IncreaseLifeTimeError',
                'message' => 'Error in increase token lifetime: ' . $th->getMessage(),
            ], 400);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $input = $request->validate(['email' => 'required|email|max:255']);

        /** @var User $user */
        $user = User::where('email', $input['email'])->first();
        if (!$user) {
            return response()->json([
                'error' => null,
                'message' => 'Reset password link sent to your email, if email already register!',
            ]);
        }

        // Remove all tokens to log out the user
        $user->tokens()->delete();

        // Remove all old reset password data
        PasswordReset::where('email', $user->email)->delete();

        // Create a new token to verify the user
        $resetPasswordData = [
            'email' => $user->email,
            'token' => sha1(time()),
        ];
        PasswordReset::create($resetPasswordData);

        // TODO: Send email to user

        return response()->json([
            'error' => null,
            'message' => 'Reset password link sent to your email, if email already register.',
        ]);
    }

    /**
     * @param Request $request
     * @param string $token
     * @return JsonResponse
     * @throws NotFoundException
     * @throws TokenExpiredException
     */
    public function checkToken(string $token): JsonResponse
    {
        $resetPass = PasswordReset::where('token', $token)->first(['created_at']);
        if (!$resetPass) {
            throw new NotFoundException('Reset password token not found!');
        }

        $generateInMinutes = $resetPass->created_at->diffInMinutes(Carbon::now());
        if ($generateInMinutes > self::RESET_PASSWORD_TOKEN_EXPIRED) {
            throw new TokenExpiredException();
        }

        return response()->json([
            'error' => null,
            'message' => 'Token is valid!',
        ]);
    }

    /**
     * @param Request $request
     * @param string $token
     * @return JsonResponse
     * @throws NotFoundException
     * @throws TokenExpiredException
     */
    public function newPassword(Request $request, string $token): JsonResponse
    {
        $input = $request->validate([
            'new_password' => 'required|string|min:8',
        ]);

        $resetPass = PasswordReset::with('user')->where('token', $token)->first();
        if (!$resetPass) {
            throw new NotFoundException('Reset password token not found!');
        }

        $generateInMinutes = $resetPass->created_at->diffInMinutes(Carbon::now());
        if ($generateInMinutes > self::RESET_PASSWORD_TOKEN_EXPIRED) {
            throw new TokenExpiredException();
        }

        $user = $resetPass->user;
        $user->password = Hash::make($input['new_password']);
        $user->save();

        // Remove all tokens to log out the user
        $user->tokens()->delete();

        // Remove all old reset password data
        PasswordReset::where('email', $user->email)->delete();

        // TODO: Dispatch job to notify user

        return response()->json([
            'error' => null,
            'message' => 'Password has been changed successfully!',
        ]);
    }

    /**
     * @return UserResource
     */
    public function me(): UserResource
    {
        /** @var User $user */
        $user = auth()->user();

        return UserResource::make($user);
    }
}
