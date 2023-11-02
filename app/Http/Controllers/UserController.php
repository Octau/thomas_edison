<?php

namespace App\Http\Controllers;

use App\Http\Helpers\RequestHelper;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserLiteResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\QueryBuilder\Admin\UserQueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserController extends Controller
{
    public function __construct(private UserQueryBuilder $userQueryBuilder)
    {
    }

    public function index(Request $request): ResourceCollection
    {
        $users = $this->userQueryBuilder->getQueryBuilder($request->input('q'));

        return UserLiteResource::collection($users->paginate(RequestHelper::limit($request)))
            ->additional($this->userQueryBuilder->getResource($request));
    }

    public function show(User $user): UserResource
    {
//        $user->load($user->getLoad());

        return new UserResource($user);
    }

    public function store(UserRequest $request): UserResource
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $employee = new User($validated);
            $employee->password = Hash::make($validated['password']);
            $employee->saveOrFail();
            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return (new UserResource($employee))
            ->additional([
                'message' => __('success.store_user_success'),
            ]);
    }

    public function update(UserRequest $request, User $user): UserResource
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            $user->fill($validated);
            if (array_key_exists('password', $validated) && $validated['password'] !== null) {
                $user->password = Hash::make($validated['password']);
            }
            $user->saveOrFail();

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
            throw $throwable;
        }

        return (new UserResource($user))
            ->additional([
                'message' => __('success.update_user_success'),
            ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->revoke();
        $user->delete();

        return response()->json([
            'message' => __('success.delete_user_success'),
        ]);
    }
}
