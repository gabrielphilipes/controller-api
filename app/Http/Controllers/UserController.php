<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidPermissionException;
use App\Exceptions\NoDestroyYourselfException;
use App\Http\Requests\RegisterAuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $businessId = auth()->user()->business_id;

        $users = User::whereBusinessId($businessId)->get();

        return UserResource::collection($users);
    }

    /**
     * @param RegisterAuthRequest $request
     * @return UserResource
     * @throws InvalidPermissionException
     */
    public function store(RegisterAuthRequest $request): UserResource
    {
        $permissions = $request->permissions ?? ['*'];

        $permissionsAccepted = User::getAllPermissions();
        foreach ($permissions as $permission) {
            if ($permission !== '*' && !in_array($permission, $permissionsAccepted)) {
                throw new InvalidPermissionException('Permission not accepted. Accepted only: ' . implode(', ', $permissionsAccepted) . '.');
            }
        }

        $user = User::create([
            'business_id' => auth()->user()->business_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'permissions' => $permissions,
        ]);

        // TODO: Send welcome email to user

        return UserResource::make($user);
    }

    /**
     * @param User $user
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        /*
         * Due to the scope, configure in the model, it is not necessary
         * to check if the user belongs to the same business.
         */
        return UserResource::make($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * @param User $user
     * @return void
     * @throws NoDestroyYourselfException
     */
    public function destroy(User $user): void
    {
        if ($user->id === auth()->user()->id) {
            throw new NoDestroyYourselfException();
        }

        // Remove all tokens to user, preventing to login
        $user->tokens()->delete();

        $user->delete();
    }
}
