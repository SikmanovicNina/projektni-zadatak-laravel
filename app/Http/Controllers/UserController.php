<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\ResponseCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = in_array($request->input('per_page', 20), self::PER_PAGE_OPTIONS)
            ? $request->input('per_page', 20)
            : 20;
        $filters = $request->only(['search', 'role_id']);

        $users = $this->userService->getAllUsers($filters, $perPage);

        return response()->json([
            'status' => 'success',
            'data' => new ResponseCollection($users, UserResource::class),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();

        $user = $this->userService->createUser($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function show(User $user)
    {
        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UserRequest $request, User $user)
    {
        $validatedData = $request->validated();

        $user = $this->userService->updateUser($user, $validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user)
    {
        $this->userService->deleteUser($user);

        return response()->json(['message' => 'User deleted successfully.'], 200);
    }

    /**
     * Upload and store the user's profile picture.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     */
    public function uploadPicture(Request $request, User $user)
    {
        $request->validate([
            'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        $picture = $request->file('picture');

        $path = $this->userService->uploadProfilePicture($user, $picture);

        return response()->json([
            'message' => 'Picture uploaded successfully',
            'picture_path' => $path,
        ], 200);
    }
}
