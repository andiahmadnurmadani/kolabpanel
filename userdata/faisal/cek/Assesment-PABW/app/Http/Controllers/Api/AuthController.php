<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Handle user registration via API
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'birth_date' => 'nullable|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle photo upload if provided
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'donatur', // Default role for new users (matches database enum)
            'coins' => 0, // Default coins for new users
            'photo' => $photoPath,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
        ]);

        // Authenticate user for web session as well
        Auth::login($user);

        // Create token for the new user
        $token = $user->createToken('auth-token')->plainTextToken;

        $responseData = [
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'coins' => $user->coins,
                'photo' => $user->photo,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date ? $user->birth_date->format('Y-m-d') : null,
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ];

        $response = response()->json($responseData, 201);

        \Log::info('Registration response:', ['response' => $response->getContent()]);

        return $response;
    }

    /**
     * Handle user login via API
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $response = response()->json([
                'message' => 'Invalid credentials',
                'errors' => [
                    'email' => ['Email atau password salah. Silakan coba lagi.']
                ]
            ], 422);

            \Log::info('Login failed response:', ['response' => $response->getContent()]);

            return $response;
        }

        // Logout any existing web session first to avoid conflicts
        Auth::logout();

        // Authenticate user for web session as well
        Auth::login($user);

        // Revoke all existing tokens for the user
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        $responseData = [
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'coins' => $user->coins,
                'photo' => $user->photo,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date ? $user->birth_date->format('Y-m-d') : null,
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ];

        $response = response()->json($responseData);

        \Log::info('Login successful response:', ['response' => $response->getContent()]);

        return $response;
    }

    /**
     * Handle user logout via API
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        // Also logout from web session
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = response()->json([
            'message' => 'Logout successful'
        ]);

        \Log::info('Logout response:', ['response' => $response->getContent()]);

        return $response;
    }

    /**
     * Get authenticated user info
     */
    public function me(Request $request): JsonResponse
    {
        // Check if user is authenticated via API token or web session
        $apiUser = $request->user();
        $webUser = auth()->check() ? auth()->user() : null;

        if (!$apiUser && $webUser) {
            // If not authenticated via API but web session exists, return web user
            $user = $webUser;
        } else {
            $user = $apiUser;
        }

        if (!$user) {
            return response()->json([
                'error' => 'User not authenticated',
                'message' => 'No authenticated user found'
            ], 401);
        }

        $responseData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'coins' => $user->coins,
                'photo' => $user->photo,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date ? $user->birth_date->format('Y-m-d') : null,
            ]
        ];

        $response = response()->json($responseData);

        \Log::info('Me response:', ['response' => $response->getContent()]);

        return $response;
    }

    /**
     * Refresh the authentication token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Also update the web session if it exists
        if (auth()->check() && auth()->id() === $user->id) {
            auth()->login($user);
        }

        $responseData = [
            'message' => 'Token refreshed successfully',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'coins' => $user->coins,
                'photo' => $user->photo,
                'phone' => $user->phone,
                'birth_date' => $user->birth_date ? $user->birth_date->format('Y-m-d') : null,
            ],
            'token' => $token,
            'token_type' => 'Bearer'
        ];

        $response = response()->json($responseData);

        \Log::info('Refresh response:', ['response' => $response->getContent()]);

        return $response;
    }
}