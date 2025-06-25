<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validate input (Laravel 9 still supports $request->validate)
        $data = $request->validate([
            'id'       => ['required', 'integer', 'exists:users,id'],
            'password' => ['required', 'string'],
        ]);

        // 2. Fetch user (safe because we just validated the ID)
        $user = User::with(['department' => fn ($q) => $q->where('active', true)])->findOrFail($data['id']);

        // 3. Verify password
        if (! Hash::check($data['password'], $user->password)) {
            return response()->json(
                ['message' => 'Mot de passe Erroné'],
                401                 // HTTP_UNAUTHORIZED
            );
        }

        $department = $user->department()->first()->name ?? '*';
        $token    = $user->createToken('auth', [$department])->plainTextToken;

        // 5. Respond with token + role (add any extra fields you need)
        return response()->json([
            'token' => $token,
            'department' => $department,
            'user'  => $user,
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
