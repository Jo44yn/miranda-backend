<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Authenticate administrative portal access keys.
     */
    public function login(Request $request)
    {
        // 1. Validate incoming payload structure shapes
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Hardcoded Administrative match definitions
        // (When you hook up your MySQL users table later, you will swap this with: Auth::attempt($credentials))
        $staticAdminEmail = 'admin@casamiranda.com';
        $staticAdminPassword = 'Password123';

        if ($credentials['email'] === $staticAdminEmail && $credentials['password'] === $staticAdminPassword) {
            return response()->json([
                'success' => true,
                'message' => 'Authentication successful. Access granted.',
                'user' => [
                    'name' => 'Casa Miranda Admin',
                    'email' => $staticAdminEmail
                ]
            ], 200);
        }

        // 3. Return a clean JSON validation response back to your JavaScript form if it fails
        return response()->json([
            'success' => false,
            'message' => 'The provided security credentials do not match our resort files.'
        ], 401);
    }
}