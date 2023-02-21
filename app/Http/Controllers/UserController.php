<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Http\Requests\RegisterAuthRequest;
use App\Exceptions\InvalidPermissionException;
use App\Exceptions\NoDestroyYourselfException;
use App\Http\Requests\CreateMultipleUsersRequest;
use App\Exceptions\AllEmailsHasRegisterException;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $users = new User();

        $withTrashed = boolval(\Illuminate\Support\Facades\Request::get('withTrashed'));
        if ($withTrashed) {
            $users = $users->withTrashed();
        }

        $users = $users->get();

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
            'business_id' => Auth::user()->business_id,
            'name' => $request->name,
            'email' => $request->email,
            'status' => 'active',
            'password' => Hash::make($request->password),
            'permissions' => $permissions,
        ]);

        // TODO: Send welcome email to user

        return UserResource::make($user);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function checkEmail(Request $request): JsonResponse
    {
        $input = $request->validate(['email' => 'required|email']);

        $existsUser = boolval(User::where('email', $input['email'])->exists());

        return response()->json(['exists' => $existsUser]);
    }

    /**
     * @param CreateMultipleUsersRequest $request
     * @return AnonymousResourceCollection
     * @throws AllEmailsHasRegisterException
     * @throws InvalidPermissionException
     */
    public function storeMultiple(CreateMultipleUsersRequest $request): AnonymousResourceCollection
    {
        $input = $request->validated();

        $usersExists = array_filter($input, function ($user) {
            return !User::where('email', $user['email'])->exists();
        });

        if (count($usersExists) === 0) {
            throw new AllEmailsHasRegisterException();
        }

        foreach ($usersExists as $user) {
            if (empty($user['password'])) {
                $user['password'] = uniqid();

                // TODO: Send email to create password
            }

            $newRequest = new RegisterAuthRequest($user);
            $response[] = $this->store($newRequest);
        }

        return UserResource::collection(collect($response));
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
     * @return JsonResponse
     * @throws NoDestroyYourselfException
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === Auth::user()?->id) {
            throw new NoDestroyYourselfException();
        }

        // Remove all tokens to user, preventing to login
        $user->tokens()->delete();

        $user->email = 'delete_' . time() . '_' . $user->email;
        $user->status = 'delete';
        $user->save();

        $user->delete();

        return response()->json(['deleted' => true]);
    }

    /**
     * @return UserResource
     */
    public function me(): UserResource
    {
        return UserResource::make(Auth::user());
    }
}
