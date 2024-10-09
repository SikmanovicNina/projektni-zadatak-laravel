<?php

namespace App\Http\Controllers;

use App\Events\PasswordResetRequested;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);

        if (!in_array($perPage, self::PER_PAGE_OPTIONS)) {
            $perPage = 20;
        }

        $users = User::latest()->filter($request->only(['search', 'role_id']))->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'data' => UserResource::collection($users)
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

        $user = User::create($validatedData);

        if ($validatedData['role_id'] === User::ROLE_LIBRARIAN) {
            event(new PasswordResetRequested($user));
        }

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
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
        $user = $user->load('role');

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
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

        $user->update($validatedData);

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
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
        $user->delete();

        return response()->json([
            'status' => 'success',
            'data' => new UserResource($user)
        ]);
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

        $path = $request->file('picture')->store('user-pictures', 'public');
        $user->profile_picture = $path;

        $user->save();

        return response()->json([
            'message' => 'Picture uploaded successfully',
            'picture_path' => $path,
        ], 200);
    }
}
