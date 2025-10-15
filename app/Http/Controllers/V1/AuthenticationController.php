<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\AuthenticationRepositoryInterface;
use Illuminate\Http\JsonResponse;

/**
 * @group Authentication
 * 
 * Authentication endpoints for the Translation Management API.
 */
class AuthenticationController extends Controller
{
    private $repository;

    public function __construct(AuthenticationRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Login
     * 
     * Authenticate user and receive an API token for accessing protected endpoints.
     * 
     * @unauthenticated
     * 
     * @bodyParam email string required User's email address. Example: test@example.com
     * @bodyParam password string required User's password. Example: password
     * 
     * @response 200 scenario="success" {
     *   "token": "1|abcdef123456789..."
     * }
     * 
     * @response 401 scenario="invalid credentials" {
     *   "message": "Invalid credentials"
     * }
     * 
     * @response 500 scenario="server error" {
     *   "error": "Login failed"
     * }
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);
            $bearerToken = $this->repository->getToken($validated);
            if (!$bearerToken) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            return response()->json(['token' => $bearerToken], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Login failed'], 500);
        }
    }
}
