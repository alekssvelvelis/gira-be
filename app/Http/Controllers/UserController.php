<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Organization;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return response()->json($request->user());
    }

    public function index(Request $request, $userId)
    {
        return response()->json(User::findOrFail($userId));
    }

    public function update(Request $request, User $user)
    {
        $isChangingPassword = $request->filled('new_password') || $request->filled('confirmed_new_password');

        $validated = $request->validate([
            'nickname' => 'required|string|max:255|unique:users,nickname,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_picture' => $request->hasFile('profile_picture')
                ? 'image|mimes:jpeg,jpg,png|max:2048'
                : 'nullable',
            'password' => 'required|string|current_password',
            'new_password' => $isChangingPassword
                ? 'required|string|min:8'
                : 'nullable|string|min:8',
            'confirmed_new_password' => $isChangingPassword
                ? 'required|string|min:8|same:new_password'
                : 'nullable|string|min:8',
        ]);

        $user->update([
            'nickname' => $validated['nickname'],
            'email' => $validated['email'],
        ]);

        if ($isChangingPassword) {
            $user->update([
                'password' => \Hash::make($validated['new_password']),
            ]);
        }

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && \Storage::disk('public')->exists($user->profile_picture)) {
                \Storage::disk('public')->delete($user->profile_picture);
            }
            $file = $request->file('profile_picture');
            $extension = $file->extension();
            $filename = 'user_' . $user->id . '.' . $extension;
            $path = $file->storeAs('profiles', $filename, 'public');
            $user->update(['profile_picture' => $path]);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }

    public function userOrganization(Request $request)
    {
        $organizations = $request->user()->organizations;
        return response()->json($organizations);
    }
}
