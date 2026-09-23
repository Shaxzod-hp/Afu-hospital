<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate admin user and issue Sanctum token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'nullable|string',
            'email' => 'nullable|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('username') ?? $request->input('email') ?? $request->input('login');

        if (!$login) {
            throw ValidationException::withMessages([
                'username' => ['Login foydalanuvchi nomi yoki email kiritilishi shart.'],
            ]);
        }

        // Exact match only — no guessable fallback shortcuts
        $user = User::where('email', $login)
            ->orWhere('name', $login)
            ->first();

        $password = $request->input('password');

        // Always run Hash::check even if user is null, using a dummy hash,
        // so response timing doesn't reveal whether the login exists.
        $hashToCheck = $user->password ?? '$2y$10$invalidinvalidinvalidinvalidinvalidinvalidinvalidinva';
        $passwordValid = Hash::check($password, $hashToCheck);

        if (!$user || !$passwordValid) {
            throw ValidationException::withMessages([
                'username' => ['Login yoki parol noto\'g\'ri.'],
            ]);
        }

        if (!in_array($user->role, ['admin'])) {
            throw ValidationException::withMessages([
                'username' => ['Sizda ushbu panelga kirish huquqi yo\'q.'],
            ]);
        }

        // Issue new Sanctum token
        $user->tokens()->delete();
        $token = $user->createToken('admin_token', ['admin'])->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user' => $request->user(),
        ]);
    }

    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Tizimdan chiqildi.',
        ]);
    }

    /**
     * Change user password.
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['Joriy parol noto\'g\'ri kiritildi.'],
            ]);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Parol muvaffaqiyatli o\'zgartirildi.',
        ]);
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // not_regex: Laravel 10 email qoidasidagi CRLF zaifligiga (GHSA-5vg9-5847-vvmq) qarshi
            'email' => ['required', 'email', 'max:255', 'not_regex:/[\r\n]/', 'unique:users,email,' . $request->user()->id],
        ]);

        $user = $request->user();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil ma\'lumotlari muvaffaqiyatli yangilandi.',
            'user' => $user,
        ]);
    }
}
