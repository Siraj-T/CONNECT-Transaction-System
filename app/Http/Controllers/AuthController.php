<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ==========================================
    // Web Session Authentication (Browser Admin)
    // ==========================================

    /**
     * Display the web login view.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming web login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated web session.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    // ==========================================
    // API Token Authentication (Sanctum Citizens)
    // ==========================================

    /**
     * Authenticate citizen and issue a Sanctum Bearer token.
     */
    public function apiLogin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])
            ->orWhere('phone', $validated['email'])
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password.'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account has been deactivated.'
            ], 403);
        }

        // Generate Sanctum Bearer token
        $token = $user->createToken('api-access-token')->plainTextToken;

        return response()->json([
            'message'      => 'Login successful',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'role'  => $user->getRoleNames()->first() ?? 'citizen',
            ],
        ], 200);
    }

    /**
     * Revoke the current Sanctum API access token.
     */
    public function apiLogout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.'
        ], 200);
    }

    /**
     * Return authenticated citizen profile with wallet and citizen data.
     */
    public function apiMe(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $request->user()->load(['citizen', 'wallet']),
        ], 200);
    }

    /**
     * Return authenticated citizen wallet and transaction ledgers.
     */
    public function apiWallet(Request $request): JsonResponse
    {
        $user = $request->user();
        $wallet = $user->wallet()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00, 'currency' => 'LYD']
        );

        return response()->json([
            'wallet'  => $wallet->load('ledgers'),
            'balance' => $wallet->balance,
            'available_balance' => $wallet->available_balance,
            'currency' => $wallet->currency,
        ], 200);
    }
}
