<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Authenticate user and issue a new Sanctum API token.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.',
                'errors'  => [
                    'email' => ['Las credenciales proporcionadas son incorrectas.'],
                ],
            ], 422);
        }

        $token = $user->createToken('kita-mobile')->plainTextToken;

        return response()->json([
            'token'          => $token,
            'user'           => $user->load('profiles'),
            'active_profile' => $user->profile,
        ]);
    }

    /**
     * Register a new user with their initial artist profile.
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'theme'    => 'nullable|string|in:kita-neon,cyber-purple,volt-orange,electric-red',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $slug = Str::slug($validated['name']) . '-' . uniqid();

        $profile = $user->profiles()->create([
            'name'          => $user->name,
            'slug'          => $slug,
            'bio'           => '',
            'theme'         => $request->input('theme', 'kita-neon'),
            'instruments'   => [],
            'genres'        => [],
            'coverage_area' => [],
            'widget_status' => [
                'agenda'    => true,
                'media'     => true,
                'instagram' => '',
                'spotify'   => '',
                'whatsapp'  => '',
                'facebook'  => '',
                'youtube'   => '',
            ],
        ]);

        $token = $user->createToken('kita-mobile')->plainTextToken;

        return response()->json([
            'token'          => $token,
            'user'           => $user->load('profiles'),
            'active_profile' => $profile,
        ], 201);
    }

    /**
     * Get authenticated user profile with their artist profiles.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('profiles');

        return response()->json([
            'user'           => $user,
            'active_profile' => $user->profile,
        ]);
    }

    /**
     * Revoke the current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada exitosamente.',
        ]);
    }
}
