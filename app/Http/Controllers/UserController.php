<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        //
    }

    public function store(UserRequest $request)
    {
        $profilePicturePath = null;
        if ($request->hasFile('profilePicture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        $validatedData = $request->validated();

        if ($profilePicturePath) {
            $validatedData['profile_picture'] = $profilePicturePath;
        }

        $user = User::create($validatedData);

        return response()->json($user, 201);
    }

    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
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
