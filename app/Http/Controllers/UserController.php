<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $perPage = request('perPage', 20);

        $users = User::latest()->filter(request(['search', 'role_id']))->paginate($perPage);

        return response()->json($users);
    }

    public function store(UserRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::create($validatedData);

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        $user = $user->load('role');
        return response()->json($user, 200);
    }

    public function update(UserRequest $request, User $user)
    {
        $validatedData = $request->validated();

        $user->update($validatedData);

        return response()->json($user, 200);

    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(204);
    }

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
